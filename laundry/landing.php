<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laundry Master</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
     <!-- Favicon -->
     <link rel="icon" href="asset/img/lm.png" type="image/x-icon">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      background-color: #f8f9fa;
      color: #333;
    }

    header {
      background-color: #333333;
      color: white;
      text-align: left;
      padding: 1.5rem 1rem;
    }

    header h1 {
      margin: 0;
      font-size: 2.5rem;
      text-align: left;
    }

    header p {
      margin: 0.5rem 0 0;
      font-size: 1.2rem;
    }

    .hero {
      background: url('asset/img/bg.jpg') center/cover no-repeat;
      color: #526D82;
      text-align: center;
      padding: 15rem 8rem;
    }

    .hero h1 {
      font-size: 3rem;
      color: white;
      margin-bottom: 1rem;
    }

    .hero p {
      font-size: 1.2rem;
      color: white;
      margin-bottom: 2rem;
    }

    .cta-button {
      background-color: #FFC107;
      color: #333;
      padding: 1rem 2rem;
      font-size: 1.2rem;
      text-decoration: none;
      border-radius: 5px;
    }

    .cta-button:hover {
      background-color: #FFA000;
    }

    .services {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 3rem 1rem;
      gap: 2rem;
    }

    .service-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 1.5rem;
      text-align: center;
      width: 300px;
    }

    .service-card h3 {
      font-size: 1.5rem;
      color: #007BFF;
      margin-bottom: 1rem;
    }

    .service-card p {
      font-size: 1rem;
      color: #555;
    }

    .price {
      font-size: 1.2rem;
      color: #040505;
      font-weight: bold;
      margin-top: 1rem;
    }

    .about {
      background-color: #e9ecef;
      padding: 3rem 1rem;
      text-align: center;
    }

    .about h2 {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    .about p {
      font-size: 1.2rem;
      margin: 0 auto;
      max-width: 800px;
      color: #555;
    }

    .contact {
      background-color: #f4f4f9;
      text-align: center;
      padding: 2rem;
    }

    .contact h2 {
      margin-bottom: 1rem;
    }

    .contact p {
      margin: 1rem 0;
      font-size: 1rem;
    }

    footer {
      background-color: #333333;
      color: white;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
    }

    .login-button {
      background-color: #FFC107;
      color: #333;
      padding: 1rem 2rem;
      font-size: 1.2rem;
      text-decoration: none;
      border-radius: 5px;
    }

    .login-button:hover {
      background-color: #FFA000;
    }

    .features {
      background-color: #fff;
      padding: 3rem 1rem;
      text-align: center;
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      justify-content: center;
    }

    .feature-card {
      background: white;
      border: 1px solid #ddd;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 1.5rem;
      text-align: center;
      width: 300px;
    }

    .feature-card h3 {
      font-size: 1.5rem;
      color: #007BFF;
      margin-bottom: 1rem;
    }

    .feature-card p {
      font-size: 1rem;
      color: #555;
    }
    

  </style>
</head>
<body>
    <header>
        <h1>Laundry Master</h1>
        <p>Clean Clothes, Happy Life</p>
    </header>
      <section class="hero">
        <h1>Your Trusted Laundry Partner</h1>
        <p>Quick, Affordable, and Reliable Laundry Services Tailored for You</p>
        <a href="bookings.php" class="cta-button">Book Now</a>
      </section>

      <section class="features">
        <div class="feature-card">
          <h3>Eco-friendly Cleaning</h3>
          <p>We use environmentally friendly detergents and processes.</p>
        </div>
        <div class="feature-card">
          <h3>Fast Turnaround</h3>
          <p>Get your laundry done within 24 hours.</p>
        </div>
        <div class="feature-card">
          <h3>Affordable Pricing</h3>
          <p>High-quality service at competitive rates.</p>
        </div>
        <div class="feature-card">
          <h3>Specialized Care</h3>
          <p>Delicate fabrics are treated with special care.</p>
        </div>
        <div class="feature-card">
          <h3>Pickup & Delivery</h3>
          <p>Convenient door-to-door laundry service.</p>
        </div>
      </section>

      <section class="about">
        <h2>About Us</h2>
        <p>Laundry Master has been a trusted name in laundry services for over a decade. Our mission is to provide top-quality care for your clothes while saving you time and effort. With state-of-the-art equipment and a dedicated team, we ensure that every garment is treated with the utmost care and attention. Whether you need daily laundry services or specialized cleaning, we’ve got you covered.</p>
      </section>

      <section class="services">
        <div class="service-card">
          <h3>Laundry Master & Dry Laundry Services</h3>
          <p>📌Price per load (maximum of 7KG)</p>
          <p class="price">✔️Wash - 65 Pesos</p>
          <p class="price">✔️Dry - 75 Pesos</p>
          <p class="price">✔️Detergent - 30 Pesos</p>
          <p class="price">✔️Full Service (Wash, Dry, Fold + Detergent & FabCon) - 170 Pesos</p>  
        </div>
      </section>

      <section id="contact" class="contact">
        <h2>Contact Us</h2>
        <p>Have questions or need assistance? Reach out to us!</p>
        <p>Email: laundrymaster@gmail.com | Phone:  +63-2-8822-7687</p>
      </section>

      <footer>
        <a href="#" class="login-button">Login</a>
        <p>&copy; 2024 Laundry Master. All rights reserved.</p>
      </footer>
    </body>
 </html>
