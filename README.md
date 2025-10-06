# Seamless Payment (Laravel + UPI Sandbox)

A demo Laravel project to integrate UPI payment flow using a sandbox API.  
Users can enter an amount, create a transaction, fetch QR & UPI link, check status, and see success feedback.

---

## 🧩 Features & Flow

- User inputs amount  
- Click **Pay / Create Transaction** → sends request to sandbox `/create-transaction`  
- Receive a token in response  
- Use token to call `/get-deposit-details` → get:
  - QR code (base64 or image)
  - UPI link (upi://pay?...), clickable & copyable
  - Amount  
- Display **Pending** status via `/validate-transaction`  
- Provide **Check status** button to refresh status  
- On **success**, show a message (toast or banner) and reload page  
- Friendly error handling & loader animation  

---

## 📂 Project Structure (key files)




---

## 🛠 Setup Instructions

#### Clone the repository  
   ```bash
   git clone https://github.com/pandey-ajay-tech/seamless-payment.git
   cd seamless-payment


 #### Install Composer dependencies

    composer install

#### Configure .env file
    Copy .env.example to .env and set:
    
    APP_URL=http://127.0.0.1:8000
    MERCHANT_KEY=your_sandbox_merchant_key


  ### Serve the application

     php artisan serve



### Find the video of assignment output 

    file name :  Seamless-Payment-Demo.mp4


🔄 How to Test / Use
###########################

    Enter an amount (≥ 1)
    Click “Pay / Create Transaction”
    You will see QR + UPI Link, and status badge
    Click “Check status” to poll until status becomes “success”
    On success, toast/banner appears and then page reloads



