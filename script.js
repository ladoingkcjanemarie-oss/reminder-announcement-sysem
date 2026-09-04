async function sendSMS(event) {
    event.preventDefault();
    
    let rawNumber = document.getElementById('mobileNumber').value.trim().replace(/[\s\-\(\)]/g, '');
    const message = document.getElementById('smsMessage').value.trim();
    
    // Format Philippine Mobile Number to 639XXXXXXXXX
    if (rawNumber.length === 10 && rawNumber.startsWith('9')) {
        rawNumber = '63' + rawNumber;
    } else if (rawNumber.startsWith('09')) {
        rawNumber = '63' + rawNumber.substring(1);
    } else if (rawNumber.startsWith('+63')) {
        rawNumber = '63' + rawNumber.substring(3);
    }

    if (!/^639\d{9}$/.test(rawNumber)) {
        alert('Invalid Philippine mobile number format.');
        return;
    }

    const payload = {
        api_token: "ce7dec7661f500ee7aa06ba2b1a60d0f8509836f",
        message: message,
        phone_number: rawNumber
    };

    try {
        const response = await fetch("https://www.iprogsms.com/api/v1/sms_messages", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });
        
        const data = await response.json();
        alert("Announcement dispatched successfully!");
    } catch (error) {
        alert("Failed to send message via Gateway API.");
    }
}