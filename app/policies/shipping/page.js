import Navbar from '../../../components/Navbar';
import styles from '../../page.module.css';

export default function ShippingPolicy() {
  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ padding: '120px 24px 80px', maxWidth: '800px', margin: '0 auto', background: 'white', minHeight: '100vh' }}>
        <h1 style={{ fontSize: '48px', fontWeight: '800', marginBottom: '24px', color: 'var(--primary)' }}>Shipping & Delivery</h1>
        <div style={{ fontSize: '18px', color: '#4a5568', lineHeight: '1.8' }}>
          <p>We deliver our premium nuts and dry fruits across India.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>Processing Time</h2>
          <p>All orders are processed within 1-2 business days. Orders are not shipped or delivered on weekends or holidays.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>Shipping Rates</h2>
          <p>Shipping charges for your order will be calculated and displayed at checkout.</p>
        </div>
      </div>
    </main>
  );
}
