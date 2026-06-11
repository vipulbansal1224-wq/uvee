import Navbar from '../../../components/Navbar';
import ImageSlider from '../../../components/ImageSlider';
import styles from '../../page.module.css';

export default function PrivacyPolicy() {
  const images = [
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.14-PM.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.15-PM-1.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.16-PM-1.jpeg'
  ];

  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ height: '300px', overflow: 'hidden' }}>
        <ImageSlider images={images} />
      </div>
      <div style={{ padding: '60px 24px 80px', maxWidth: '800px', margin: '0 auto', background: 'white', minHeight: '100vh' }}>
        <h1 style={{ fontSize: '48px', fontWeight: '800', marginBottom: '24px', color: 'var(--primary)' }}>Privacy Policy</h1>
        <div style={{ fontSize: '18px', color: '#4a5568', lineHeight: '1.8' }}>
          <p>At UVEE, we are committed to protecting your privacy. This policy explains how we collect, use, and protect your personal information.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>Information We Collect</h2>
          <p>We may collect personal information such as your name, email address, and shipping address when you place an order.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>How We Use Your Information</h2>
          <p>We use the information we collect to process your orders, communicate with you, and improve our services.</p>
        </div>
      </div>
    </main>
  );
}
