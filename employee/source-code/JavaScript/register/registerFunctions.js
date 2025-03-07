document.addEventListener('DOMContentLoaded', function () {
    const userType = document.getElementById('register_userType');
    const companyName = document.getElementById('register_CompanyName');

    userType.addEventListener('sl-change', function () {
        if (userType.value === 'Company') {
            companyName.removeAttribute('disabled');
        } else {
            companyName.setAttribute('disabled', true);
        }
    });
});