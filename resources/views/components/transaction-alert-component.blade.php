@if($transactionsCount > 0)

    <div class="notification" role="status">{{$transactionsCount}}</div>
@endif

<style>
    .notification {

        /* circle shape, size and position */
        position: absolute;
        @if (App::getLocale() === 'ar')
        left: 1.5rem;
        @else
        right: 1.5rem;
        @endif
        top: 1.6rem;
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
