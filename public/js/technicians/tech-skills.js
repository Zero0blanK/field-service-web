document.getElementById('addSkillsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('ajax/update_technician_skills.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to reflect new skills
            location.reload();
        } else {
            alert('Failed to update skills: ' + data.message);
        }
    });
});

function removeSkill(skillId) {
    if (confirm('Are you sure you want to remove this skill?')) {
        fetch('ajax/remove_technician_skill.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                skill_id: skillId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to remove skill: ' + data.message);
            }
        });
    }
}