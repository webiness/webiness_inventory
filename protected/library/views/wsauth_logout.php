<?php
    $url = WsUrl::link('site', 'index');
?>

<script>
    setTimeout(function () {
        window.location.href='<?php echo $url; ?>'; // the redirect goes here
    }, 100);
</script>


