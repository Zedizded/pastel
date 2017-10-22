function display() {
    if(document.getElementById('fos_user_registration_form_pastelMember').checked) {
        document.getElementById('members').style.display = 'block';
    } else {
        document.getElementById('members').style.display = 'none';
    }
}