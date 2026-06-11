import Navbar from '../../components/Navbar';
import ImageSlider from '../../components/ImageSlider';
import styles from '../page.module.css';

export default function Contact() {
  const images = [
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.07-PM-1.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.08-PM-1.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.09-PM-1.jpeg'
  ];

  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ height: '300px', overflow: 'hidden' }}>
        <ImageSlider images={images} />
      </div>
      <div style={{ padding: '60px 24px 80px', maxWidth: '800px', margin: '0 auto' }}>
        <h1 style={{ fontSize: '48px', fontWeight: '800', marginBottom: '24px', color: 'var(--primary)' }}>Contact Us</h1>
        
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '40px', marginTop: '40px' }}>
          <div>
            <h3 style={{ fontSize: '24px', marginBottom: '16px' }}>Get in Touch</h3>
            <p style={{ color: '#4a5568', marginBottom: '8px' }}><strong>SETHI SPICES</strong></p>
            <p style={{ color: '#4a5568', marginBottom: '8px' }}>Shed No.7, Ladowal Mega Food Park,<br/>Ludhiana, Punjab - 141008</p>
            <p style={{ color: '#4a5568', marginBottom: '8px' }}><strong>Phone:</strong> +91 7347400909</p>
            <p style={{ color: '#4a5568', marginBottom: '8px' }}><strong>Email:</strong> sethispices@gmail.com</p>
            <p style={{ color: '#4a5568', marginTop: '20px' }}><strong>GST No:</strong> 03CLXPS2482L1ZA</p>
            <p style={{ color: '#4a5568', marginBottom: '8px' }}><strong>Fssai Lic No:</strong> 12120441000039</p>
          </div>
          
          <div style={{ background: 'white', padding: '30px', borderRadius: '16px', boxShadow: 'var(--shadow-sm)' }}>
            <h3 style={{ fontSize: '24px', marginBottom: '16px' }}>Send us a message</h3>
            <form style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
              <input type="text" placeholder="Your Name" style={{ padding: '12px', border: '1px solid #e2e8f0', borderRadius: '8px' }} />
              <input type="email" placeholder="Your Email" style={{ padding: '12px', border: '1px solid #e2e8f0', borderRadius: '8px' }} />
              <textarea placeholder="Your Message" rows="4" style={{ padding: '12px', border: '1px solid #e2e8f0', borderRadius: '8px', resize: 'none' }}></textarea>
              <button type="button" style={{ padding: '12px', background: 'var(--primary)', color: 'white', border: 'none', borderRadius: '8px', fontWeight: 'bold', cursor: 'pointer' }}>Send Message</button>
            </form>
          </div>
        </div>

        {/* Google Map */}
        <div style={{ marginTop: '60px', borderRadius: '16px', overflow: 'hidden', boxShadow: 'var(--shadow-md)', height: '400px' }}>
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3423.4752538186175!2d75.7667498!3d30.9015949!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391a82f3c30a58ad%3A0x8e8b1509ba249f3e!2sMega%20Food%20Park%20Ladowal!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
            width="100%" 
            height="100%" 
            style={{ border: 0 }} 
            allowFullScreen="" 
            loading="lazy" 
            referrerPolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </main>
  );
}
