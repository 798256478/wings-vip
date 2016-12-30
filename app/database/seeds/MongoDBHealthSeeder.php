<?php


use App\Models\Health\Detection;
use App\Models\Questionnaire;
use App\Models\QuestionnaireAnswer;
use Illuminate\Database\Seeder;


class MongoDBHealthSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{


//		问卷表

		$questionnaire = new Questionnaire;
//		$res = $questionnaire->first()->delete();
		$questionnaire->name = '个人健康信息调查表';//问卷的名字
		$questionnaire->sections = [
			[
				'section' => '基本情况',
				'questions' => [
					[
						'type' => 'text',
						'title' => '姓    名',
						'explain' => '',
						'is_required' => '1',
						'position' => 'topic_1',

					],
					[
						'type' => 'text',
						'title' => '身份证号',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_2',

					],
					[
						'type' => 'text',
						'unique' =>'birthday',
						'title' => '出生日期',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_3',

					],
					[
						'type' => 'text',
						'title' => '民    族',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_4',

					],
					[
						'type' => 'choose',
						'title' => '性    别',
						'explain' => '',
						'is_required' => '1',
						'position' => 'topic_5',
						'options' => [
							'a' => '男',
							'b' => '女',
						]
					],
					[
						'type' => 'choose',
						'title' => '婚    姻',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_6',
						'options' => [
							'a' => '未婚',
							'b' => '已婚',
							'c' => '离异',
							'd' => '丧偶',
							'e' => '其它',
						]
					],
					[
						'type' => 'choose',
						'title' => '文化程度',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_7',
						'options' => [
							'a' => '文盲',
							'b' => '小学',
							'c' => '初中',
							'd' => '大专',
							'e' => '高中与中专',
							'f' => '本科以上',
						]
					],
					[
						'type' => 'choose',
						'title' => '职    业',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_8',
						'options' => [
							'a' => '工人',
							'b' => '农民',
							'c' => '待业',
							'd' => '家务',
							'e' => '其它',
							'f' => '教师',
							'g' => '金融',
							'h' => '离退休',
							'i' => '机关干部',
							'j' => '医药卫生',
							'k' => '科技人员',
							'l' => '公司职员',
						]
					],
					[
						'type' => 'text',
						'title' => '通讯地址',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_9',

					],
					[
						'type' => 'text',
						'title' => '联系电话',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_10',

					]
				]
			],
			[
				'section' => '目前健康状况',
				'questions' => [
					[
						'type' => 'choose',
						'title' => '总体来讲，你的健康状况是',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_11',
						'options' => [
							'a' => '非常好',
							'b' => '好',
							'c' => '一般',
							'd' => '差',
						]
					],
					[
						'type' => 'choose',
						'title' => '您过去一段时间感到疲劳的程度',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_12',
						'options' => [
							'a' => '无疲劳',
							'b' => '稍微疲劳',
							'c' => '很疲劳',
							'd' => '非常疲劳',
						]
					],
					[
						'type' => 'choose',
						'title' => '同一年前相比，您的体重是',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_13',
						'options' => [
							'a' => '增加',
							'b' => '基本不变',
							'c' => '下降',
							'd' => '不清楚',
						]
					],
					[
						'type' => 'judge',
						'title' => '在近一年内，您曾试图减过体重吗？',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_14',
						'options' => [
							'a' => '否',
							'b' => '是',
						]
					],
					[
						'type' => 'judge',
						'title' => '您近半年内测过血压吗？',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_15',
						'options' => [
							'a' => '未测',
							'b' => '测过',
						]
					],
					[
						'type' => 'judge',
						'title' => '您近半年内测过血脂吗？',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_16',
						'options' => [
							'a' => '未测',
							'b' => '测过',
						]
					],
					[
						'type' => 'judge',
						'title' => '是否经常有颈部、腰部、骨关节疼痛',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_17',
						'options' => [
							'a' => '否',
							'b' => '是',
						]
					],
					[
						'type' => 'judge',
						'title' => '是否有慢性腹泻、便秘、大便不正常',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_18',
						'options' => [
							'a' => '否',
							'b' => '是',
						]
					],
					[
						'type' => 'checkbox',
						'title' => '慢性疾病史',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_19',
						'options' => [
							'a' => '肥胖',
							'b' => '痛风',
							'c' => '高血压',
							'd' => '冠心病',
							'e' => '糖尿病',
							'f' => '血脂异常',
							'g' => '脑卒中',
							'h' => '脂肪肝',
							'i' => '下肢动脉闭塞',
							'j' => '多囊卵巢综合症',
						]
					],
					[
						'type' => 'checkbox',
						'title' => '家族史中您祖父是否有一下病症',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_20',
						'options' => [
							'a' => '肥胖',
							'b' => '高血压',
							'c' => '冠心病',
							'd' => '糖尿病',
							'e' => '高血脂',
							'f' => '脑卒中',
							'g' => '代谢综合症',
							'h' => '下肢动脉闭塞',
						]
					],
					[
						'type' => 'checkbox',
						'title' => '家族史中您祖母是否有一下病症',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_21',
						'options' => [
							'a' => '肥胖',
							'b' => '高血压',
							'c' => '冠心病',
							'd' => '糖尿病',
							'e' => '高血脂',
							'f' => '脑卒中',
							'g' => '代谢综合症',
							'h' => '下肢动脉闭塞',
						]
					],
					[
						'type' => 'checkbox',
						'title' => '家族史中您父亲是否有一下病症',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_22',
						'options' => [
							'a' => '肥胖',
							'b' => '高血压',
							'c' => '冠心病',
							'd' => '糖尿病',
							'e' => '高血脂',
							'f' => '脑卒中',
							'g' => '代谢综合症',
							'h' => '下肢动脉闭塞',
						]
					],
					[
						'type' => 'checkbox',
						'title' => '家族史中您母亲是否有一下病症',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_23',
						'options' => [
							'a' => '肥胖',
							'b' => '高血压',
							'c' => '冠心病',
							'd' => '糖尿病',
							'e' => '高血脂',
							'f' => '脑卒中',
							'g' => '代谢综合症',
							'h' => '下肢动脉闭塞',
						]
					],
					[
						'type' => 'checkbox',
						'title' => '家族史中您兄弟是否有一下病症',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_24',
						'options' => [
							'a' => '肥胖',
							'b' => '高血压',
							'c' => '冠心病',
							'd' => '糖尿病',
							'e' => '高血脂',
							'f' => '脑卒中',
							'g' => '代谢综合症',
							'h' => '下肢动脉闭塞',
						]
					],
					[
						'type' => 'checkbox',
						'title' => '家族史中您姐妹是否有一下病症',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_25',
						'options' => [
							'a' => '肥胖',
							'b' => '高血压',
							'c' => '冠心病',
							'd' => '糖尿病',
							'e' => '高血脂',
							'f' => '脑卒中',
							'g' => '代谢综合症',
							'h' => '下肢动脉闭塞',
						]
					],
					[
						'type' => 'choose',
						'title' => '家族居住情况',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_26',
						'options' => [
							'a' => '20年',
							'b' => '40年',
							'c' => '60年',
							'd' => '80年',
							'e' => '100年以上',
						]
					],
					[
						'type' => 'choose',
						'title' => '个人居住情况',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_27',
						'options' => [
							'a' => '20年',
							'b' => '40年',
							'c' => '60年',
							'd' => '80年',
							'e' => '100年以上',
						]
					],
				]
			],
			[
				'section' => '膳食与运动',
				'questions' => [
					[
						'type' => 'choose',
						'title' => '每日主副食比例',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_28',

						'options' => [
							'a' => '主食为主',
							'b' => '主副食各半',
							'c' => '主食为辅',
							'd' => '副食为主',
						]
					],
					[
						'type' => 'choose',
						'title' => '豆制品摄入量',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_29',

						'options' => [
							'a' => '每天吃',
							'b' => '经常吃',
							'c' => '偶尔吃',
							'd' => '不吃',
						]
					],
					[
						'type' => 'choose',
						'title' => '奶制品摄入量',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_30',

						'options' => [
							'a' => '每天吃',
							'b' => '经常吃',
							'c' => '偶尔吃',
							'd' => '不吃',
						]
					],
					[
						'type' => 'choose',
						'title' => '平均每天吃蔬菜',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_31',

						'options' => [
							'a' => '≥8两',
							'b' => '5-7两',
							'c' => '2-4两',
							'd' => '＜2两',
						]
					],
					[
						'type' => 'choose',
						'title' => '平均每天吃水果',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_32',

						'options' => [
							'a' => '≥5两',
							'b' => '3-4两',
							'c' => '≤2两',
							'd' => '不吃',
						]
					],
					[
						'type' => 'choose',
						'title' => '平均每天吃鸡蛋',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_33',

						'options' => [
							'a' => '≥3个',
							'b' => '2个',
							'c' => '1个',
							'd' => '<1个',
						]
					],
					[
						'type' => 'choose',
						'title' => '平均每天吃鱼和肉',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_34',

						'options' => [
							'a' => '≥8两',
							'b' => '5-7两',
							'c' => '2-4两',
							'd' => '≤1两',
						]
					],
					[
						'type' => 'choose',
						'title' => '每人每月植物油消费量',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_35',

						'options' => [
							'a' => '>4斤',
							'b' => '3-4斤',
							'c' => '2-3斤',
							'd' => '<2斤',
						]
					],
					[
						'type' => 'choose',
						'title' => '每人每月食盐消费量',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_36',

						'options' => [
							'a' => '≥8两',
							'b' => '6-7两',
							'c' => '4-5两',
							'd' => '<4两',
						]
					],
					[
						'type' => 'choose',
						'title' => '您常吃早餐吗？',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_37',

						'options' => [
							'a' => '每天吃',
							'b' => '经常吃',
							'c' => '偶尔吃',
							'd' => '不吃',
						]
					],
					[
						'type' => 'choose',
						'title' => '您通常一日吃几餐',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_38',

						'options' => [
							'a' => '两餐',
							'b' => '三餐',
							'c' => '四餐',
							'd' => '五餐以上',
						]
					],
					[
						'type' => 'choose',
						'title' => '每日吃辛辣食品',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_39',

						'options' => [
							'a' => '每天吃',
							'b' => '经常吃',
							'c' => '偶尔吃',
							'd' => '不吃',
						]
					],
					[
						'type' => 'choose',
						'title' => '工作或日常生活中（8小时）坐着的时间',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_40',

						'options' => [
							'a' => '几乎全部',
							'b' => '多余4小时',
							'c' => '少于4小时',
							'd' => '几乎没有',
						]
					],
					[
						'type' => 'choose',
						'title' => '近距离（3公里以内）外出办事，您主要的出行方式是',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_41',

						'options' => [
							'a' => '步行',
							'b' => '骑自行车',
							'c' => '乘车或开车',
							'd' => '很少外出办事',
						]
					],
					[
						'type' => 'choose',
						'title' => '一般情况下，外出办事您往返所用的时间大概是多少分钟',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_42',

						'options' => [
							'a' => '≤10',
							'b' => '11-30',
							'c' => '31-60',
							'd' => '≥60',
						]
					],
					[
						'type' => 'choose',
						'title' => '日常生活中的拖地、擦窗等家务劳动',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_43',

						'options' => [
							'a' => '经常',
							'b' => '有时',
							'c' => '很少',
							'd' => '没有',
						]
					],
					[
						'type' => 'judge',
						'title' => '您参加体育锻炼吗',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_44',

						'options' => [
							'a' => '否',
							'b' => '是',
						]
					],
					[
						'type' => 'choose',
						'title' => '如果参加，您最常用的锻炼方式是（只选一个最常用的）',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_45',

						'options' => [
							'a' => '散步',
							'b' => '跑步',
							'c' => '自行车',
							'd' => '舞蹈或太极拳',
							'e' => '上下楼梯',
							'f' => '球类',
							'g' => '游泳',
							'h' => '其他',
						]
					],
					[
						'type' => 'choose',
						'title' => '您平均每周锻炼的次数',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_46',

						'options' => [
							'a' => '≤2次',
							'b' => '3-4次',
							'c' => '≥5次',
						]
					],
					[
						'type' => 'choose',
						'title' => '平均每次锻炼的时间是多少分钟',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_47',

						'options' => [
							'a' => '≤20',
							'b' => '21-40',
							'c' => '41-60',
							'd' => '≥60',
						]
					],
				]
			],
			[
				'section' => '行为习惯',
				'questions' => [
					[
						'type' => 'choose',
						'title' => '有无被动吸烟',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_48',

						'options' => [
							'a' => '经常',
							'b' => '偶尔',
							'c' => '很少',
							'd' => '从无',
						]
					],
					[
						'type' => 'choose',
						'title' => '是否吸烟',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_49',

						'options' => [
							'a' => '否',
							'b' => '是',
							'c' => '偶吸',
							'd' => '已戒',
						]
					],
					[
						'type' => 'choose',
						'title' => '每日吸烟支数',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_50',

						'options' => [
							'a' => '1-5支',
							'b' => '6-10支',
							'c' => '11-20支',
							'd' => '20支以上',
						]
					],
					[
						'type' => 'choose',
						'title' => '吸烟年数',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_51',

						'options' => [
							'a' => '1年内',
							'b' => '1-5年',
							'c' => '6-10年',
							'd' => '11年以上',
						]
					],
					[
						'type' => 'choose',
						'title' => '是否经常饮酒',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_52',

						'options' => [
							'a' => '否',
							'b' => '是',
							'c' => '很少',
							'd' => '已戒',
						]
					],
					[
						'type' => 'choose',
						'title' => '主要饮酒种类',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_53',

						'options' => [
							'a' => '白酒',
							'b' => '啤酒',
							'c' => '果酒',
							'd' => '其他',
						]
					],
					[
						'type' => 'text',
						'title' => '每日平均饮酒量多少ml',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_54',

					],
					[
						'type' => 'choose',
						'title' => '在过去一个月时间里，您精力充沛吗',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_55',

						'options' => [
							'a' => '大部分时间',
							'b' => '较多时间',
							'c' => '小部分时间',
							'd' => '没有此感觉',
						]
					],
					[
						'type' => 'choose',
						'title' => '在过去一个月时间里，您生活得充实吗',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_56',

						'options' => [
							'a' => '大部分时间',
							'b' => '较多时间',
							'c' => '小部分时间',
							'd' => '没有此感觉',
						]
					],
					[
						'type' => 'choose',
						'title' => '你感到垂头丧气，什么事都不能使您振作起来吗',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_57',

						'options' => [
							'a' => '否',
							'b' => '是',
						]
					],
					[
						'type' => 'choose',
						'title' => '睡眠状况',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_58',

						'options' => [
							'a' => '很差',
							'b' => '差',
							'c' => '一般',
							'd' => '良好',
						]
					],
					[
						'type' => 'choose',
						'title' => '睡眠时间',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_59',

						'options' => [
							'a' => '＜6小时',
							'b' => '6-8小时',
							'c' => '9-10小时',
							'd' => '＞10小时',
						]
					],
					[
						'type' => 'choose',
						'title' => '经常熬夜吗',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_60',

						'options' => [
							'a' => '经常',
							'b' => '偶尔',
							'c' => '很少',
							'd' => '无',
						]
					],
					[
						'type' => 'choose',
						'title' => '对自己以前的事业满意度',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_61',

						'options' => [
							'a' => '差',
							'b' => '一般',
							'c' => '满意',
							'd' => '非常满意',
						]
					],
					[
						'type' => 'choose',
						'title' => '对自己脾气评价',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_62',

						'options' => [
							'a' => '急脾气',
							'b' => '一般',
							'c' => '温和',
						]
					],
					[
						'type' => 'choose',
						'title' => '与同事合作状况',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_63',

						'options' => [
							'a' => '差',
							'b' => '一般',
							'c' => '融洽',
						]
					],
					[
						'type' => 'choose',
						'title' => '夫妻关系',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_64',

						'options' => [
							'a' => '差',
							'b' => '一般',
							'c' => '较好',
							'd' => '非常好',
						]
					],
					[
						'type' => 'choose',
						'title' => '性生活质量',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_65',

						'options' => [
							'a' => '差',
							'b' => '一般',
							'c' => '较好',
							'd' => '很好',
						]
					],
					[
						'type' => 'choose',
						'title' => '性生活次数 （每周）',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_66',

						'options' => [
							'a' => '1-2次',
							'b' => '3-4次',
							'c' => '5-6次',
							'd' => '6次以上',
						]
					],
					[
						'type' => 'choose',
						'title' => '与子女关系',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_67',

						'options' => [
							'a' => '差',
							'b' => '一般',
							'c' => '较好',
							'd' => '很好',
						]
					],

				]
			],
			[
				'section' => '体格检查',
				'questions' => [
					[
						'type' => 'text',
						'title' => '身高（cm）',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_68',

					],
					[
						'type' => 'text',
						'title' => '体重（kg）',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_69',

					],
					[
						'type' => 'text',
						'title' => '腰围（cm）',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_70',

					],
					[
						'type' => 'text',
						'title' => '血压（mmHg）',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_71',

					],
					[
						'type' => 'text',
						'title' => '总胆固醇(mmol/L)',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_72',

					],
					[
						'type' => 'text',
						'title' => '甘油三脂(mmol/L)',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_73',

					],
					[
						'type' => 'text',
						'title' => '高密度脂蛋白(mmol/L)',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_74',

					],
					[
						'type' => 'text',
						'title' => '低密度脂蛋白(mmol/L)',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_75',

					],
					[
						'type' => 'text',
						'title' => '空腹血糖(mmol/L)',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_76',

					],
					[
						'type' => 'text',
						'title' => '餐后2小时血糖(mmol/L)',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_77',

					],
					[
						'type' => 'text',
						'title' => '糖化血红蛋白（%）',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_78',

					],
					[
						'type' => 'choose',
						'title' => 'B超脂肪肝',
						'explain' => '',
						'is_required' => '0',
						'position' => 'topic_79',

						'options' => [
							'a' => '无',
							'b' => '轻度',
							'c' => '中度',
							'd' => '重复',
						]
					],

				]
			]
		];


		$questionnaire->save();


//		答卷表
//		$answer = new QuestionnaireAnswer;
//		$answer->questionnaire_id = '';//指向问卷
//		$answer->topic_1 = 'a';
//		$answer->topic_2 = true;
//
//		$answer->save();

	}
}
