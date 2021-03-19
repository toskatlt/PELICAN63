var visible = true;
function showFun() {
    if(visible) {
        document.getElementById('video' ).style.display = 'none';
        visible = false;
    } else {
        document.getElementById('video' ).style.display = 'block';
        visible = true;
    }
}
