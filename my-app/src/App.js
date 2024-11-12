import React, { useEffect, useState } from 'react';
import axios from 'axios';
import useRazorpay from "react-razorpay";

function RazorpayIntegration() {
  const [Razorpay] = useRazorpay();
  const [paymentSuccess, setPaymentSuccess] = useState(false);
  const [paymentSuccessResponse, setPaymentSuccessResponse] = useState()

  // useEffect(() => {
  //   // if (paymentSuccess == true) {
  //   // }
  // }, [])
  
  // function fetchPaymentDetail() {
  //   const response1 = axios.post('http://127.0.0.1:8000/api/razorpay/fetch-payment-detail', {
  //     "razorpay_payment_id": "pay_Mu4kLeenJXPjdG",
  //     "razorpay_order_id": "order_Mu4kCSJinHbKo8",
  //     "razorpay_signature": "d6df4ec6d90d71590746a53f187175542684bfc7de5ea2441de563fb06bbffb4"
  // }); // Adjust the URL to match your Laravel route
  //   console.log(response1)
  // }
  // fetchPaymentDetail()

  const createRazorpayOrder = async () => {
    try {
      
      const response = await axios.post('http://127.0.0.1:8000/api/razorpay/create-order'); // Adjust the URL to match your Laravel route
      const options = {
        key: process.env.REACT_APP_RAZORPAY_KEY, // Replace with your Razorpay key
        order_id: response.data.order_id,
        name: 'A&B',
        description: 'LifeLine',
        handler: function (response) {
          console.log(response)
          // Handle successful payment
          setPaymentSuccessResponse(response);
          setPaymentSuccess(true);
        },
      };
      const rzp = new Razorpay(options);
      rzp.open();
    } catch (error) {
      console.error('Error creating Razorpay order:', error);
    }
  };

  return (
    <div>
      <h2>Razorpay Integration</h2>
      {paymentSuccess ? (
        <p>Payment successful!</p>
      ) : (
        <button onClick={createRazorpayOrder}>Pay with Razorpay</button>
      )}
    </div>
  );
}

export default RazorpayIntegration;
