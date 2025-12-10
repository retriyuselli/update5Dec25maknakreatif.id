<form id="auto-logout" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>
<script>
    const t = {{ (int) config('session.lifetime') * 60 * 1000 }};
    let x;

    function s() {
        clearTimeout(x);
        x = setTimeout(() => {
            document.getElementById('auto-logout').submit()
        }, t)
    } ['mousemove', 'keydown', 'scroll', 'click', 'touchstart', 'touchmove'].forEach(e => addEventListener(e, s, {
        passive: true
    }));
    s();
</script>
