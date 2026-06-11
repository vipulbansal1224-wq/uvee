import Navbar from '../../../components/Navbar';
import styles from '../../page.module.css';

export default function TermsAndConditions() {
  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ padding: '120px 24px 80px', maxWidth: '800px', margin: '0 auto', background: 'white', minHeight: '100vh' }}>
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
