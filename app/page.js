import Navbar from '../components/Navbar';
import HeroBanner from '../components/HeroBanner';
import ProductCard from '../components/ProductCard';
import { products } from '../data/products';
import styles from './page.module.css';

export default function Home() {
  return (
    <main className={styles.main}>
      <Navbar />
      <HeroBanner />
      
      <section className={styles.section}>
        <div className={styles.container}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>Featured Products</h2>
            <p className={styles.sectionSubtitle}>Handpicked premium quality dry fruits and snacks</p>
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
          <div className={styles.footerContent}>
            <div className={styles.footerBrand}>
              <h3>UVEE</h3>
              <p>Premium Quality Dry Fruits</p>
            </div>
            <div className={styles.footerLinks}>
              <a href="#">Privacy Policy</a>
              <a href="#">Terms of Service</a>
              <a href="#">Contact Us</a>
            </div>
          </div>
          <p className={styles.copyright}>© 2026 UVEE. All rights reserved.</p>
        </div>
      </footer>
    </main>
  );
}
