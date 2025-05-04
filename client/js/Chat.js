document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.querySelector('textarea');

    textarea.addEventListener('input', function () {
        this.style.height = '30px'; // Reset height
        this.style.height = this.scrollHeight + 'px'; // Set new height
    });
});