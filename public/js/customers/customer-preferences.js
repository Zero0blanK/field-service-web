
// Load technicians for select dropdown
loadTechnicians();

// Load existing preferences
loadExistingPreferences();

// Set up event listener for form submission
document.getElementById('update_preferences').addEventListener('click', function() {
    savePreferences();
});

async function loadTechnicians() {
    try {
        const response = await fetch('/user/profile/get-tech-preferences');
        if (!response.ok) {
            throw new Error('Failed to load technicians');
        }
        
        const technicians = await response.json();
        const techSelect = document.getElementById('preferences_Technician');
        
        // Clear loading option
        techSelect.innerHTML = '';
        
        // Add blank option
        const blankOption = document.createElement('sl-option');
        blankOption.value = '';
        blankOption.textContent = 'No preference';
        techSelect.appendChild(blankOption);
        
        // Add technicians to select
        technicians.forEach(tech => {
            const option = document.createElement('sl-option');
            option.value = tech.tech_id;
            option.textContent = tech.name;
            techSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading technicians:', error);
        showNotification('Failed to load technicians', 'danger');
    }
}

async function loadExistingPreferences() {
    try {
        const response = await fetch('/api/customer/preferences');
        if (!response.ok) {
            throw new Error('Failed to load preferences');
        }
        
        const preferences = await response.json();
        
        if (preferences) {
            // Set preferred technician
            if (preferences.preferred_technician) {
                document.getElementById('preferences_Technician').value = preferences.preferred_technician;
            }
            
            // Set preferred day/time
            if (preferences.preferred_day_time) {
                document.getElementById('preferences_DayTime').value = preferences.preferred_day_time;
            }
            
            // Set preferred contact method
            if (preferences.preferred_contact_method) {
                document.getElementById('preferences_Method').value = preferences.preferred_contact_method;
            }
        }
    } catch (error) {
        console.error('Error loading preferences:', error);
    }
}

async function savePreferences() {
    const preferredTechnician = document.getElementById('preferences_Technician').value;
    const preferredDayTime = document.getElementById('preferences_DayTime').value;
    const preferredMethod = document.getElementById('preferences_Method').value;
    
    try {
        const response = await fetch('/customer/preferences', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'preferred_technician': preferredTechnician,
                'preferred_day_time': preferredDayTime,
                'preferred_method': preferredMethod
            })
        });
        
        if (!response.ok) {
            throw new Error('Failed to save preferences');
        }
        
        showNotification('Preferences saved successfully', 'success');
    } catch (error) {
        console.error('Error saving preferences:', error);
        showNotification('Failed to save preferences', 'danger');
    }
}

function showNotification(message, type = 'info') {
    const toast = document.createElement('sl-alert');
    toast.variant = type;
    toast.closable = true;
    toast.duration = 3000;
    toast.innerText = message;
    document.body.appendChild(toast);
    toast.toast();
}