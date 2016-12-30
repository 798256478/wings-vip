<?php

return [
    'recipient_options' => [
        'SELF' => '本人',
        'REFERRER' => '推荐人',
        'ROOT_REFERRER' => '二级推荐人',
    ],

    'jobs' => [
        [
            'class' => App\Jobs\GiveBonus::class,
            'label' => '赠积分',
            'recipient_options' => ['SELF', 'REFERRER'],
            'term_type' => 'int',
        ],
        [
            'class' => App\Jobs\GiveBalance::class,
            'label' => '赠储值',
            'recipient_options' => ['SELF', 'REFERRER'],
            'term_type' => 'number',
        ],
        [
            'class' => App\Jobs\GiveTicket::class,
            'label' => '赠券',
            'recipient_options' => ['SELF', 'REFERRER'],
            'term_type' => [
                'ticketTemplateId' => [
                    'label' => '优惠券',
                    'type' => 'select',
                ],
                'count' => [
                    'label' => '数量',
                    'type' => 'int',
                ],
            ],
        ],
        [
            'class' => App\Jobs\GiveProperty::class,
            'label' => '赠服务',
            'recipient_options' => ['SELF', 'REFERRER'],
            'term_type' => [
                'propertyTemplateId' => [
                    'label' => '服务',
                    'type' => 'select',
                ],
                'count' => [
                    'label' => '次数',
                    'type' => 'int',
                ],
                'validity_days' => [
                    'label' => '效期',
                    'type' => 'int',
                ],
            ],
        ],
        [
            'class' => App\Jobs\LevelUp::class,
            'label' => '提升等级',
            'recipient_options' => ['SELF', 'REFERRER'],
            'term_type' => 'int',
        ],
        [
            'class' => App\Jobs\PayCommission::class,
            'label' => '返佣',
            'recipient_options' => ['REFERRER', 'ROOT_REFERRER'],
            'term_type' => [
                'commission' => [
                    'label' => '佣金',
                    'type' => 'commission',
                ],
            ],
        ],
    ],

    'events' => [
        [
            'class' => App\Events\CardCreated::class,
            'label' => '会员卡创建',
            'conditions' => [
                [
                    'key' => NULL,
                    'label' => '无条件',
                    'term_type' => NULL,
                ]
            ]
        ],
        [
            'class' => App\Events\ReferrerBound::class,
            'label' => '推荐人绑定',
            'conditions' => [
                [
                    'key' => NULL,
                    'label' => '无条件',
                    'term_type' => NULL,
                ]
            ]
        ],
        [
            'class' => App\Events\ProfileFilled::class,
            'label' => '会员卡资料填写',
            'conditions' => [
                [
                    'key' => NULL,
                    'label' => '无条件',
                    'term_type' => NULL,
                ]
            ]
        ],
        [
            'class' => App\Events\ProgressChanged::class,
            'label' => '进度更改',
            'conditions' => [
                [
                    'key' => NULL,
                    'label' => '无条件',
                    'term_type' => NULL,
                ]
            ]
        ],
        [
            'class' => App\Events\CodeRedeemed::class,
            'label' => '兑换码兑换',
            'conditions' => [
                [
                    'key' => NULL,
                    'label' => '无条件',
                    'term_type' => NULL,
                ]
            ]
        ],
        [
            'class' => App\Events\TicketVerified::class,
            'label' => '卡券核销',
            'conditions' => [
                [
                    'key' => NULL,
                    'label' => '无条件',
                    'term_type' => NULL,
                ]
            ]
        ],
        [
            'class' => App\Events\OrderCompleted::class,
            'label' => '消费',
            'conditions' => [
                [
                    'key' => NULL,
                    'label' => '无条件',
                    'term_type' => NULL,
                ],
                [
                    'key' => 'FIRST',
                    'label' => '初次消费',
                    'term_type' => NULL,
                ],
                [
                    'key' => 'EXPENSE_COUNT',
                    'label' => '累计消费次数',
                    'term_type' => 'int',
                ],
                [
                    'key' => 'TOTAL_EXPENSE',
                    'label' => '累计消费满额',
                    'term_type' => [
                        'min' => [
                            'label' => '大于',
                            'type' => 'number',
                        ],
                        'max' => [
                            'label' => '小于',
                            'type' => 'number',
                        ],
                    ],
                ],
                [
                    'key' => 'SINGLE_EXPENSE',
                    'label' => '单次消费满额',
                    'term_type' => [
                        'min' => [
                            'label' => '大于',
                            'type' => 'number',
                        ],
                        'max' => [
                            'label' => '小于',
                            'type' => 'number',
                        ],
                    ],
                ],
            ]
        ],
        [
            'class' => App\Events\SignEvent::class,
            'label' => '签到',
            'conditions' => [
                [
                    'key' => 'days',
                    'label' => '签到天数',
                    'term_type' => [
                        'min' => [
                            'label' => '大于',
                            'type' => 'number',
                        ],
                        'max' => [
                            'label' => '小于',
                            'type' => 'number',
                        ],
                    ],
                ],
            ]
        ],
    ],
];
