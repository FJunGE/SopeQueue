<?php

return [
    'broker' => [
        'metadata-broker-list' => '127.0.0.1:9092',
        // Client group session and failure detection timeout.
        // The consumer sends periodic heartbeats (heartbeat.interval.ms) to indicate its liveness to the broker.
        // 客户端组会话故障超时时间，心跳信号，超过当前时间，消费组重平衡ms
        // 未能在规定时间内向Consumer Group 发送心态，则会导致Consumer被踢出，引发Rebalance
        'session-timeout-ms' => 6000,

        // 发送心跳请求频率参数，建议每2秒发送一次频率
        // 即：session.timeout.ms = 3 * heartbeat.interval.ms 6秒的心跳发送时间，每2秒发送一次
        'heartbeat-interval-ms' => 2000,

        // Default timeout for network requests.
        // 网络请求的默认超时。ms
        'socket-timeout-ms' => 15000,

        // Alias for message.send.max.retries: How many times to retry sending a failing Message.
        // Note: retrying may cause reordering unless enable.idempotence is set to true.
        // 网络抖动重连次数 可能会造成消息重复等问题需要结合实际幂等性机制(enable.idempotence)
        'message-send-max-retries' => 3,

        // 开启broker幂等性操作 or 操作事务
        'enable-idempotence' => true,

        // 元数据定期更新时间，即使元数据本身没有发生任何变化 或 broker追加 默认9分钟
        // 当定期更新的元数据中发现追加broker tcp的io连接
        'metadata-max-age-ms' => 600000,

        // connections-max-idle-ms 定期关闭没有请求的tcp连接 设置-1则永久不关闭
        // 保持长连接，会导致很多冗余的僵尸连接
        'log-connection-close' => true,

    ],

    'producer' => [

        // producer 成功写入消息方式
        // 0: producer只要发出消息，无论消息有没有落盘都意为成功写入消息
        // 1: leader副本成功接收到消息，无论其他follow副本是否有同步，都意为成功写入消息
        // -1/all: leader副本成功接收到消息且follow副本也成功同步，则标记为成功写入消息
        'request-required-acks' => 'all',


    ],

    'consumer' => [

        // 初始偏移位置 从earliest最早的位置
        'auto-offset-reset' => 'earliest',

        // 事务隔离级别，消费者读级别
        'isolation-level' => 'read_committed',

    ],


];
