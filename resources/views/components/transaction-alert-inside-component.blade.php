@if($transactionsInsideCount > 0)

    <div class="notification_inside" role="status">{{$transactionsInsideCount}}</div>
@endif

<style>
    .notification_inside {

        /* circle shape, size and position */
        position: absolute;
        @if (App::getLocale() === 'ar')
        left: 0.8rem;
        @else
        right: 0.5rem;
        @endif
        margin-top: 0.6rem;
        min-width: 1.8em; /* or width, explained below. */
        height: 1.8em;
        border-radius: 0.8em; /* or 50%, explained below. */
        border: 0.05em solid white;
        background-color: red;

        /* number size and position */
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.8em;
        color: white;
    }
</style>
