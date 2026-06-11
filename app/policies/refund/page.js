import Navbar from '../../../components/Navbar';
import styles from '../../page.module.css';

export default function RefundPolicy() {
  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ padding: '120px 24px 80px', maxWidth: '800px', margin: '0 auto', background: 'white', minHeight: '100vh' }}>
        <h1 style={{ fontSize: '48px', fontWeight: '800', marginBottom: '24px', color: 'var(--primary)' }}>Refund Policy</h1>
        <div style={{ fontSize: '18px', color: '#4a5568', lineHeight: '1.8' }}>
          <p>We want you to be completely satisfied with your purchase. If you are not satisfied, please review our refund policy.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>Returns</h2>
          <p>You have 7 days to return an item from the date you received it. To be eligible for a return, your item must be unused and in the same condition that you received it.</p>
          <h2 style={{ marginTop: '30px', marginBottom: '15px', color: 'var(--foreground)' }}>Refunds</h2>
          <p>Once we receive your item, we will inspect it and notify you that we have received your returned item. If your return is approved, we will initiate a refund to your original method of payment.</p>
        </div>
      </div>
    </main>
  );
}
