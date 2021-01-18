window.jdenticon_config = {
    replaceMode: "observe"
};
document.addEventListener('DOMContentLoaded', function (event){
    document.querySelectorAll('pre code').forEach(function (block) {
        hljs.highlightBlock(block);
    });
});
