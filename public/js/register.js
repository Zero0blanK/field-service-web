function selectType(type) {
    const companyField = document.getElementById('company_field');
    const nameLabel = document.getElementById('name_label');
    const accountTypeInput = document.getElementById('account_type');
    const buttons = document.querySelectorAll('.account-type-btn');
    
    // Update buttons
    buttons.forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white', 'active');
        if (btn.textContent.toLowerCase().includes(type)) {
            btn.classList.add('bg-indigo-600', 'text-white', 'active');
        }
    });

    // Update form
    if (type === 'company') {
        companyField.classList.remove('hidden');
        nameLabel.textContent = 'Contact Person';
        accountTypeInput.value = 'company';
    } else {
        companyField.classList.add('hidden');
        nameLabel.textContent = 'Full Name';
        accountTypeInput.value = 'resident';
    }
}

// Initialize the form
document.querySelector('.account-type-btn').classList.add('bg-indigo-600', 'text-white');
