import Navbar from '../../components/Navbar';
import styles from '../page.module.css';

export default function Contact() {
  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ padding: '120px 24px 80px', maxWidth: '800px', margin: '0 auto' }}>
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
            <form style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
              <input type="text" placeholder="Your Name" style={{ padding: '12px', border: '1px solid #e2e8f0', borderRadius: '8px' }} />
              <input type="email" placeholder="Your Email" style={{ padding: '12px', border: '1px solid #e2e8f0', borderRadius: '8px' }} />
              <textarea placeholder="Your Message" rows="4" style={{ padding: '12px', border: '1px solid #e2e8f0', borderRadius: '8px', resize: 'none' }}></textarea>
              <button type="button" style={{ padding: '12px', background: 'var(--primary)', color: 'white', border: 'none', borderRadius: '8px', fontWeight: 'bold', cursor: 'pointer' }}>Send Message</button>
            </form>
          </div>
        </div>
      </div>
    </main>
  );
}
