import Navbar from '../../components/Navbar';
import ProductCard from '../../components/ProductCard';
import { products, categories } from '../../data/products';
import styles from '../page.module.css'; // Reuse homepage styles for layout

export default function Shop() {
  return (
    <main className={styles.main}>
      <Navbar />
      
      <div style={{ padding: '120px 24px 40px', background: 'linear-gradient(135deg, #fdfbfb 0%, #f6f3f0 100%)' }}>
        <div className={styles.container}>
          <h1 style={{ fontSize: '48px', fontWeight: '800', marginBottom: '16px' }}>Shop Our Premium Collection</h1>
          <p style={{ fontSize: '18px', color: '#718096', maxWidth: '600px' }}>
            Browse through our entire selection of carefully roasted, perfectly seasoned nuts and snacks.
          </p>
        </div>
      </div>

      <section className={styles.section} style={{ paddingTop: '60px' }}>
        <div className={styles.container}>
          
          {/* Categories Bar */}
          <div style={{ display: 'flex', gap: '16px', marginBottom: '40px', overflowX: 'auto', paddingBottom: '16px' }}>
            {categories.map((cat, idx) => (
              <span key={idx} style={{
                padding: '8px 24px', 
                borderRadius: '999px',
                background: idx === 0 ? 'var(--primary)' : 'white',
                color: idx === 0 ? 'white' : 'var(--foreground)',
                border: '1px solid var(--border-color)',
                cursor: 'pointer',
                fontWeight: '600'
              }}>
                {cat}
              </span>
            ))}
          </div>

          <div className={styles.grid}>
            {products.map(product => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </div>
      </section>

      <footer className={styles.footer}>
        <div className={styles.container}>
          <p className={styles.copyright}>© 2026 UVEE. All rights reserved.</p>
        </div>
      </footer>
    </main>
  );
}
