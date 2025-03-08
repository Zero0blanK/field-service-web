document.addEventListener('DOMContentLoaded', function () {
    const userType = document.getElementById('update_UserType');
    const companyName = document.getElementById('update_CompanyName');

    userType.addEventListener('sl-change', function () {
        if (userType.value === 'Company') {
            companyName.removeAttribute('disabled');
        } else {
            companyName.setAttribute('disabled', true);
        }
    });
});