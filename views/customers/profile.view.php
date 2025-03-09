<?php require_once VIEWS . 'partials/header.php'; ?>
<?php require_once VIEWS . 'partials/customers/sidebar.php'; ?>
<link rel="stylesheet" href="/public/css/customers/profile.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/themes/light.css" />
<script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/shoelace-autoloader.js"></script>

<div class="flex-1 p-8 ml-64">
    <div class="topNavigationBar">
        <sl-breadcrumb class="topNavbar">
            <sl-breadcrumb-item>
                <sl-icon slot="prefix" name="gear"></sl-icon>
                <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Profile Settings</label>
            </sl-breadcrumb-item>
        </sl-breadcrumb>
    </div>
    
    <div class="card-container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Account Information</h1>
                <?php if (isset($success) && $success): ?>
                    <sl-alert variant="success" open>
                        <sl-icon slot="icon" name="check2-circle"></sl-icon>
                        Your account information has been updated successfully!
                    </sl-alert>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <sl-alert variant="danger" open>
                        <sl-icon slot="icon" name="exclamation-triangle"></sl-icon>
                        <strong>There were errors with your submission:</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </sl-alert>
                <?php endif; ?>
                
                <?php if (isset($isCompany) && $isCompany): ?>
                    <div class="account-type">
                        <sl-badge variant="primary" pill>Company Account</sl-badge>
                    </div>
                <?php else: ?>
                    <div class="account-type">
                        <sl-badge variant="neutral" pill>Residential Account</sl-badge>
                    </div>
                <?php endif; ?>
            </div>
            
            <form method="POST" id="accountForm">
                <div class="form-header">
                    <sl-button variant="primary" type="button" id="editButton">
                        <sl-icon slot="prefix" name="pencil-square"></sl-icon>
                        Edit Information
                    </sl-button>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <sl-input id="update_FullName" name="update_FullName" label="Full Name" type="text" 
                                 placeholder="Enter your name" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" disabled>
                            <sl-icon name="person-square" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    
                    <div class="form-group">
                        <sl-input id="update_EmailAddress" name="update_EmailAddress" label="Email Address" type="email" 
                                 placeholder="email@example.com" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" disabled>
                            <sl-icon name="envelope" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    
                    <div class="form-group">
                        <sl-input id="update_PhoneNumber" name="update_PhoneNumber" label="Phone Number" type="text" 
                                 placeholder="(123) 456-7890" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" disabled>
                            <sl-icon name="phone" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    
                    <?php if (isset($isCompany) && $isCompany): ?>
                    <div class="form-group">
                        <sl-input id="update_CompanyName" name="update_CompanyName" label="Company Name" type="text" 
                                 placeholder="Enter your company name" value="<?= htmlspecialchars($customerInfo['company_name'] ?? '') ?>" disabled>
                            <sl-icon name="building" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <sl-input id="update_Address" name="update_Address" label="Address" type="text" 
                                 placeholder="Enter your Address" value="<?= htmlspecialchars($currentUser['address'] ?? '') ?>" disabled>
                            <sl-icon name="geo-alt" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    
                    <div class="form-group">
                        <sl-input id="update_City" name="update_City" label="City" type="text" 
                                 placeholder="Enter your city" value="<?= htmlspecialchars($currentUser['city'] ?? '') ?>" disabled>
                            <sl-icon name="buildings" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    
                    <div class="form-group">
                        <sl-input id="update_zipCode" name="update_zipCode" label="Zip Code" type="text" 
                                 placeholder="1234" value="<?= htmlspecialchars($currentUser['zipcode'] ?? '') ?>" disabled>
                            <sl-icon name="map" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    
                    <div class="form-group password-group">
                        <sl-input id="update_Password" name="update_Password" label="New Password" type="password" 
                                 placeholder="••••••••" password-toggle disabled>
                            <sl-icon name="key" slot="prefix"></sl-icon>
                        </sl-input>
                        <small class="password-hint">Leave blank to keep your current password</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <sl-button variant="primary" type="submit" id="update_submit" disabled>
                        <sl-icon slot="prefix" name="arrow-repeat"></sl-icon>
                        Update Account
                    </sl-button>
                    
                    <sl-button variant="neutral" type="button" id="cancelButton" disabled>
                        <sl-icon slot="prefix" name="x-circle"></sl-icon>
                        Cancel
                    </sl-button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.getElementById('editButton');
    const cancelButton = document.getElementById('cancelButton');
    const updateButton = document.getElementById('update_submit');
    const inputFields = document.querySelectorAll('sl-input');
    
    // Function to enable form fields
    function enableFormFields() {
        inputFields.forEach(input => {
            input.disabled = false;
        });
        updateButton.disabled = false;
        cancelButton.disabled = false;
        editButton.style.display = 'none';
    }
    
    // Function to disable form fields
    function disableFormFields() {
        inputFields.forEach(input => {
            input.disabled = true;
        });
        updateButton.disabled = true;
        cancelButton.disabled = true;
        editButton.style.display = 'inline-flex';
    }
    
    // Add event listeners
    editButton.addEventListener('click', enableFormFields);
    
    cancelButton.addEventListener('click', function(e) {
        e.preventDefault();
        // Reset form to original values
        document.getElementById('accountForm').reset();
        disableFormFields();
    });
});
</script>

<?php require_once VIEWS . 'partials/footer.php'; ?>