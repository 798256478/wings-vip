(function ($) {
    var defaults = {
        label: ['省', '市', '区'],
        areaWheels: 'PCD',
        areasKey : ['Provinces', 'Cities', 'Districts']
    };
    $.mobiscroll.presetShort('area');
    $.mobiscroll.presets.scroller.area = function (inst) {
        var orig = $.extend({}, inst.settings),
            s = $.extend(inst.settings, defaults, orig),
            wg = [],
            wheels = [],
            changeTime;//change的计时器
            headerTime ='';//headerText的计时器
        var areas = s.areaWheels.split(''), areasKey = s.areasKey;
        if (inst.settings.valueo) {
            var val = inst.settings.valueo.split(' ');
            for (var i = 0; i < areas.length; i++) {
                genWheels(getAreaInfo(i, val[i-1]), s.label[i], i);
            }
        } else {
            for (var i = 0; i < areas.length; i++) {
                genWheels(getAreaInfo(i), s.label[i], i);
            }
        }

        wheels.push(wg);     
        function getAreaInfo(index,val) {
            var area, _p, _c;
            if (areas[index] === "P") {
                area = AddressInfo[areasKey[index]];
            }
            if (areas[index] === "C") {
                _p = val||wg[0].keys[0];
                area = AddressInfo[areasKey[index]][_p];
            }
            if (areas[index] === "D") {
                _c = val||wg[1].keys[0];
                area = AddressInfo[areasKey[index]][_c];
            }
            return area;
        }

        function genWheels(area, lbl, type, isRet) {
            var values = [], keys = [];
            type = areas[type];
            if (type == "P") {
                for (var i = 0; i < area.length; i++) {
                    values.push(area[i]['_proviceid']);
                    keys.push(area[i]['_provicename']);
                }
            }
            if (type == "C") {
                for (var i = 0; i < area.length; i++) {
                    values.push(area[i]['_cityid']);
                    keys.push(area[i]['_cityname']);
                }
            }
            if (type == "D") {
                for (var i = 0; i < area.length; i++) {
                    values.push(area[i]['_districtid']);
                    keys.push(area[i]['_districtname']);
                }
            }
            if (isRet) {
                return {
                    values: keys,
                    keys: values,
                    label: lbl
                };
            }
            addWheel(wg, values, keys, lbl);
        }

        function addWheel(wg, k, v, lbl) {
            wg.push({
                values: v,
                keys: k,
                label: lbl
            });
        }
        function getAreasResult(type, value, values){
            console.log("{type="+type+", value="+value+", values="+ values+"}");
            type = areas[type];
            var area;
            if (type == "P") {
                console.log(s.areasKey[type])
                area = AddressInfo[areasKey[type]];
                console.log(area);

                $.each(area, function(val, key){
                    console.log(val);
                    if(val === value){
                        return key;
                    }
                })
            }
            if (type == "C") {
                area = AddressInfo[values[type-1]];
                $.each(area, function(val, key){
                    if(val === value){
                        return key;
                    }
                });
            }
            if (type == "D") {
                area = AddressInfo[values[type-1]];
                $.each(area, function(val, key){
                    if(val === value){
                        return key;
                    }
                });
            }
        }

        function getAddressText(id){
            var text = '';
            for(var i = 0;i < AddressInfo.Provinces.length;i++){
                if (AddressInfo.Provinces[i]._proviceid == id[0]) {
                    text += AddressInfo.Provinces[i]._provicename;
                    break;
                }
            }
            for (var i = 0; i < AddressInfo.Cities[id[0]].length; i++) {
                if (AddressInfo.Cities[id[0]][i]._cityid == id[1]) {
                    text += ' ' + AddressInfo.Cities[id[0]][i]._cityname;
                    break;
                }
            }
            for (var i = 0; i < AddressInfo.Districts[id[1]].length; i++) {
                if (AddressInfo.Districts[id[1]][i]._districtid == id[2]) {
                    text += ' ' + AddressInfo.Districts[id[1]][i]._districtname;
                    break;
                }
            }
            return text;
        }
        return {
            wheels: wheels,

            onBeforeShow: function (dw) {
                s.wheels = wheels;
            },
            onSelect: function (val) {
                $('#area').attr("areaid",val);
                $('#area').val(getAddressText(val.split(' ')));
            },
            onChange: function (index, val, wheel) {
                clearTimeout(changeTime);
                var a= val;
                //TODO:当前选择的省市区类型(0:省, 1：市)
                val = val.split(" ")[index];
                if (parseInt(index) >= 0 && index < s.label.length - 1) {
                    var data, idx = parseInt(index) + 1//(1:市， 2:区);
                    changeTime = setTimeout(function () {
                        //TODO:要改变的省市区类型
                        data = genWheels(AddressInfo[areasKey[idx]][val], s.label[idx], idx ,true);
                        //设置要改变的省市区数据
                        //wheel.setWheels(idx, data);
                        wheel.settings.wheels[0][idx] = data;
                        if(idx < s.label.length - 1){
                            var next = idx + 1, nextVal = data['keys'][0];
                            data = genWheels(AddressInfo[areasKey[next]][nextVal], s.label[next], next ,true);
                            //wheel.setWheels(next, data);
                            wheel.settings.wheels[0][next] = data;
                        }
                        // 切换改变动作
                        if(idx == 1){
                            wheel.changeWheel([idx, idx+1], 0);
                        }else if(idx == 2){
                            wheel.changeWheel([idx], 0);
                        }
                        //wheel._header.html(getAddressText(wheel._tempValue.split(' ')));
                    }, 500);
                }
            },
            //headerText:function(val){
            //        return getAddressText(val.split(' '));
            //},
            onInit:function(inst){
                var val = $("#area").attr("areaid");
                inst._wheelArray = val.split(" ");
                //inst._header.html(getAddressText(val.split(' ')));
                //val = val.split(" ");
                //$("[data-val='"+val[0]+"']").attr("aria-selected","true")

            },
        }
    };
})(jQuery);