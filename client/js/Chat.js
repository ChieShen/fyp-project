document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.querySelector('textarea');

    textarea.addEventListener('input', function () {
        this.style.height = '30px'; // Reset height
        this.style.height = this.scrollHeight + 'px'; // Set new height
    });
});

window.addEventListener('load', function () {
    const chatHistory = document.querySelector('.chatHistory');
    if (chatHistory) {
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }
});