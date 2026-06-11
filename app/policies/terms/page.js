import Navbar from '../../../components/Navbar';
import ImageSlider from '../../../components/ImageSlider';
import styles from '../../page.module.css';

export default function TermsAndConditions() {
  const images = [
    '/wp-content/uploads/2022/07/salted-almond-1.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.08-PM-1.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.15-PM-1.jpeg'
  ];

  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ height: '300px', overflow: 'hidden' }}>
        <ImageSlider images={images} />
      </div>
      <div style={{ padding: '60px 24px 80px', maxWidth: '800px', margin: '0 auto', background: 'white', minHeight: '100vh' }}>
        <h1 style={{ fontSize: '48px', fontWeight: '800', marginBottom: '24px', color: 'var(--primary)' }}>Terms & Conditions</h1>
        <div style={{ fontSize: '18px', color: '#4a5568', lineHeight: '1.8' }}>
          <p>Please read these terms and conditions carefully before using Our Service.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>Conditions of Use</h2>
          <p>We will provide their services to you, which are subject to the conditions stated below in this document. Every time you visit this website, use its services or make a purchase, you accept the following conditions.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>Privacy Policy</h2>
          <p>Before you continue using our website, we advise you to read our privacy policy regarding our user data collection.</p>
        </div>
      </div>
    </main>
  );
}
