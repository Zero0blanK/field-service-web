<?php require_once VIEWS . '/partials/header.php'; ?>
<?php require_once VIEWS . '/partials/customers/sidebar.php'; ?>
<link rel="stylesheet" href="/public/css/customers/profile-preferences.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/themes/light.css" />
<script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/shoelace-autoloader.js"></script>

<div class="flex-1 p-8 ml-64">
    <!-- Success notification toast -->
    <?php if (isset($_GET['success'])): ?>
    <sl-alert variant="success" closable class="success-alert" duration="3000">
        <sl-icon slot="icon" name="check2-circle"></sl-icon>
        Preferences updated successfully!
    </sl-alert>
    <script>
        document.querySelector('.success-alert').toast();
    </script>
    <?php endif; ?>

    <div class="topNavigationBar">          
        <sl-breadcrumb class="topNavbar">
            <sl-breadcrumb-item href="/dashboard">
                <sl-icon slot="prefix" name="house"></sl-icon>
                Dashboard
            </sl-breadcrumb-item>
            <sl-breadcrumb-item>
                <sl-icon slot="prefix" name="sliders"></sl-icon>
                Preferences
            </sl-breadcrumb-item>
        </sl-breadcrumb>
    </div>

    <div class="preferences-container">
        <div class="card-header">
            <sl-icon name="gear-fill" style="font-size: 2rem; color: #27BAFD;"></sl-icon>
            <h1>Account Preferences</h1>
        </div>

        <div class="preferences-content">
            <form id="preferencesForm" method="post" action="/user/preferences">
                <div class="form-grid">
                    <div class="form-section">
                        <h2>Service Preferences</h2>
                        
                        <sl-select id="preferences_Technician" name="preferred_technician" label="Preferred Technician" size="medium" clearable>
                            <?php if (!empty($technicians)): ?>
                                <sl-option value="">No preference</sl-option>
                                <?php foreach ($technicians as $tech): ?>
                                    <sl-option value="<?= $tech['tech_id'] ?>" <?= (isset($preferences['preferred_technician']) && $preferences['preferred_technician'] == $tech['tech_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tech['name']) ?>
                                    </sl-option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <sl-option value="">No technicians available</sl-option>
                            <?php endif; ?>
                        </sl-select>
                        
                        <sl-select id="preferences_DayTime" name="preferred_day_time" label="Preferred Day/Time" clearable>
                            <sl-option value="morning" <?= (isset($preferences['preferred_time_of_day']) && $preferences['preferred_time_of_day'] == 'morning') ? 'selected' : '' ?>>Morning</sl-option>
                            <sl-option value="afternoon" <?= (isset($preferences['preferred_time_of_day']) && $preferences['preferred_time_of_day'] == 'afternoon') ? 'selected' : '' ?>>Afternoon</sl-option>
                            <sl-option value="evening" <?= (isset($preferences['preferred_time_of_day']) && $preferences['preferred_time_of_day'] == 'evening') ? 'selected' : '' ?>>Evening</sl-option>
                            <small slot="help-text">When would you prefer us to schedule your appointments?</small>
                        </sl-select>
                    </div>
                    
                    <div class="form-section">
                        <h2>Contact Preferences</h2>
                        
                        <sl-select id="preferences_ContactMethod" name="contact_method" label="Contact Method" clearable>
                            <sl-option value="phone" <?= (isset($preferences['preferred_contact_method']) && $preferences['preferred_contact_method'] == 'phone') ? 'selected' : '' ?>>Phone</sl-option>
                            <sl-option value="email" <?= (isset($preferences['preferred_contact_method']) && $preferences['preferred_contact_method'] == 'email') ? 'selected' : '' ?>>Email</sl-option>
                            <sl-option value="text" <?= (isset($preferences['preferred_contact_method']) && $preferences['preferred_contact_method'] == 'text') ? 'selected' : '' ?>>Text Message</sl-option>
                        </sl-select>
                    </div>
                </div>
                <div class="button-group">
                    <sl-button variant="default" type="reset">
                        <sl-icon slot="prefix" name="x-circle"></sl-icon>
                        Reset
                    </sl-button>
                    <sl-button variant="primary" type="submit" id="update_preferences">
                        <sl-icon slot="prefix" name="check2-circle"></sl-icon>
                        Save Preferences
                    </sl-button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .preferences-container {
        max-width: 900px;
        margin: 2rem 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem 2rem;
        background: linear-gradient(to right, #f8f9fa, #e9ecef);
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .preferences-content {
        padding: 2rem;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .form-section {
        margin-bottom: 2rem;
    }
    
    .form-section h2 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #27BAFD;
        margin-bottom: 1.2rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }
    
    sl-select, sl-input, sl-textarea {
        margin-bottom: 1.5rem;
        --sl-input-border-radius-medium: 8px;
    }
    
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        margin-top: 1.2rem;
    }
    
    .button-group {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .preferences-container {
            margin: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Shoelace components
        const technicianSelect = document.getElementById('preferences_Technician');
        const dayTimeSelect = document.getElementById('preferences_DayTime');
        const contactMethodSelect = document.getElementById('preferences_ContactMethod');
        
        // Update the selected state after Shoelace components are initialized
        setTimeout(() => {
            <?php if (isset($preferences['preferred_technician'])): ?>
            technicianSelect.value = '<?= $preferences['preferred_technician'] ?>';
            <?php endif; ?>
            
            <?php if (isset($preferences['preferred_time_of_day'])): ?>
            dayTimeSelect.value = '<?= $preferences['preferred_time_of_day'] ?>';
            <?php endif; ?>
            
            <?php if (isset($preferences['preferred_contact_method'])): ?>
            contactMethodSelect.value = '<?= $preferences['preferred_contact_method'] ?>';
            <?php endif; ?>
        }, 100);
        
        // Form submission handler
        document.getElementById('preferencesForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitButton = document.getElementById('update_preferences');
            submitButton.loading = true;
            
            // Create form data
            const formData = new FormData(this);
            
            // Submit form using fetch
            fetch('/user/preferences', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to save preferences');
                }
                window.location.href = '/user/preferences?success=1';
            })
            .catch(error => {
                console.error('Error:', error);
                const toast = document.createElement('sl-alert');
                toast.variant = 'danger';
                toast.closable = true;
                toast.duration = 5000;
                toast.innerHTML = `
                    <sl-icon slot="icon" name="exclamation-triangle"></sl-icon>
                    Failed to save preferences. Please try again.
                `;
                document.body.appendChild(toast);
                toast.toast();
            })
            .finally(() => {
                submitButton.loading = false;
            });
        });
    });
</script>

<?php require_once VIEWS . '/partials/footer.php'; ?>