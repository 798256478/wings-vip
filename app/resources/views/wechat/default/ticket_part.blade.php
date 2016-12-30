<details class="ticket">
    <summary>
        <div class="left">
            <small class="type">{{ $ticket->ticketTemplate['card_type'] }}</small>
            <span class="title {{ $ticket->ticketTemplate['color'] }}Front">
                {{ $ticket->ticketTemplate['title'] }}
            </span>
                
        </div>
        <div class="right {{ $ticket->ticketTemplate['color'] }}">
            <span class="sub-title">{{ $ticket->ticketTemplate['sub_title'] or '无限制' }}</span>
            @if ( time() < strtotime($ticket->ticketTemplate['begin_timestamp']) )
                <small class="date-info">{{ $ticket->ticketTemplate['begin_display_time'] }}</small>
                <small class="date-info">/</small>
                <small class="date-info">{{ $ticket->ticketTemplate['end_display_time'] }}</small>
            @else
                <small class="date-info">{{ $ticket->ticketTemplate['end_display_time'] }}</small>
                <small class="date-info">到期</small>
            @endif
            
            
            @if ( strtotime($ticket->ticketTemplate['end_timestamp']) - time() < 3600 * 24 * 4 )
                <div class="seal"> 即将到期</div>
            @elseif ( strtotime($ticket->ticketTemplate['begin_timestamp']) - time() > 0 )
                <div class="seal grey">尚未开始</div>
            @endif
            
            <div class="point p1"></div>
            <div class="point p2"></div>
            <div class="point p3"></div>
            <div class="point p4"></div>
            <div class="point p5"></div>
            <div class="point p6"></div>
        </div>

    </summary>
    <p class="description">{{ $ticket->ticketTemplate['description'] }}</p>
</details>