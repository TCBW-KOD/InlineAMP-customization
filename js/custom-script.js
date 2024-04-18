document.addEventListener("DOMContentLoaded", function() {
    // 确保DOM完全加载后执行插件的代码
    if (window.yourPlugin && typeof window.yourPlugin.init === 'function') {
        window.yourPlugin.init();
    }
});
