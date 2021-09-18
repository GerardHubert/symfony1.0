const error = document.getElementsByClassName('form-error-icon');
for (e of error) {
    e.classList.remove('badge-danger');
    e.classList.add('bg-danger');
}