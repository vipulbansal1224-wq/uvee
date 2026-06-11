import Navbar from '../components/Navbar';
import HeroBanner from '../components/HeroBanner';
import ProductCard from '../components/ProductCard';
import { products } from '../data/products';
import styles from './page.module.css';

export default function Home() {
  // Only show the first 4 products on the home page as requested
  const featuredProducts = products.slice(0, 4);

  return (
    <main className={styles.main}>
      <Navbar />
      <HeroBanner />
      
      {/* WordPress Content Section */}
      <section className={styles.section} style={{ background: '#fdfbfb' }}>
        <div className={styles.container}>
          <div style={{ textAlign: 'center', maxWidth: '800px', margin: '0 auto', paddingBottom: '40px' }}>
            <h2 className={styles.sectionTitle}>Premium Quality Cashews, Pistachios and Almonds</h2>
            <p style={{ fontSize: '18px', color: '#4a5568', lineHeight: '1.8', marginBottom: '24px' }}>
              Our product line includes different quality grades of plain whole cashew nuts, traditional drum roasted cashew nuts and a variety of flavored cashew nuts. We also specialize in premium quality pistachios and almonds.
            </p>
            <p style={{ fontSize: '18px', color: '#4a5568', lineHeight: '1.8' }}>
              Our products do not follow the industrial pattern of preservation and adulteration for a minimum nutrition loss of the groceries, as we aim to focus on a longer and healthier life for people and not shelf life of products.
            </p>
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '32px', marginTop: '40px' }}>
            <div style={{ background: 'white', padding: '30px', borderRadius: '16px', boxShadow: 'var(--shadow-sm)' }}>
              <img src="/wp-content/uploads/2022/07/roasted-masala-cashew-1.jpeg" alt="Cashews" style={{ width: '100%', height: '200px', objectFit: 'cover', borderRadius: '12px', marginBottom: '20px' }} />
              <h3 style={{ fontSize: '24px', color: 'var(--primary)', marginBottom: '16px' }}>Cashews</h3>
              <p style={{ color: '#4a5568' }}>Authentic Goan cashews, farmed locally. Boasting both premium quality and the best cashew nut wholesale price in India.</p>
            </div>
            <div style={{ background: 'white', padding: '30px', borderRadius: '16px', boxShadow: 'var(--shadow-sm)' }}>
              <img src="/wp-content/uploads/2022/07/roasted-pista-1.jpeg" alt="Pistachios" style={{ width: '100%', height: '200px', objectFit: 'cover', borderRadius: '12px', marginBottom: '20px' }} />
              <h3 style={{ fontSize: '24px', color: 'var(--primary)', marginBottom: '16px' }}>Pistachios</h3>
              <p style={{ color: '#4a5568' }}>Crunchy, lightly salted pistachios, packed with nutrition for a healthy snacking option.</p>
            </div>
            <div style={{ background: 'white', padding: '30px', borderRadius: '16px', boxShadow: 'var(--shadow-sm)' }}>
              <img src="/wp-content/uploads/2022/07/salted-almond-1.jpeg" alt="Almonds" style={{ width: '100%', height: '200px', objectFit: 'cover', borderRadius: '12px', marginBottom: '20px' }} />
              <h3 style={{ fontSize: '24px', color: 'var(--primary)', marginBottom: '16px' }}>Almonds</h3>
              <p style={{ color: '#4a5568' }}>Handpicked, premium quality almonds, both delicious and a rich source of protein and healthy fats.</p>
            </div>
          </div>
        </div>
      </section>

      <section className={styles.section}>
        <div className={styles.container}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>Featured Products</h2>
            <p className={styles.sectionSubtitle}>Handpicked premium quality dry fruits and snacks</p>
          </div>
          
          <div className={styles.grid}>
            {featuredProducts.map(product => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
          
          <div style={{ textAlign: 'center', marginTop: '40px' }}>
             <a href="/shop" style={{ display: 'inline-block', padding: '12px 32px', background: 'var(--primary)', color: 'white', borderRadius: '99px', fontWeight: 'bold' }}>View All Products</a>
          </div>
        </div>
      </section>

      <footer className={styles.footer}>
        <div className={styles.container}>
          <div className={styles.footerContent}>
            <div className={styles.footerBrand}>
              <img src="/wp-content/uploads/2022/07/logo-1.jpg" alt="UVEE Logo" style={{ height: '50px', width: 'auto', marginBottom: '16px', borderRadius: '4px' }} />
              <p style={{ maxWidth: '300px' }}>Right from flower to fruit to the processing and packaging, we employ fair trade practices and high quality standards.</p>
            </div>
            <div style={{ flex: 1, marginLeft: '60px' }}>
               <h4 style={{ fontSize: '20px', color: 'white', marginBottom: '16px' }}>Contact Us</h4>
               <p style={{ color: '#a0aec0', marginBottom: '8px' }}><strong>SETHI SPICES</strong></p>
               <p style={{ color: '#a0aec0', marginBottom: '8px' }}>Shed No.7, Ladowal Mega Food Park, Ludhiana, Punjab - 141008</p>
               <p style={{ color: '#a0aec0', marginBottom: '8px' }}>GST No: 03CLXPS2482L1ZA | Fssai Lic No: 12120441000039</p>
               <p style={{ color: '#a0aec0', marginBottom: '8px' }}>Phone: +91 7347400909</p>
               <p style={{ color: '#a0aec0' }}>Email: sethispices@gmail.com</p>
            </div>
            <div className={styles.footerLinks} style={{ flexDirection: 'column' }}>
              <a href="/about">About Us</a>
              <a href="/contact">Contact Us</a>
              <a href="/shop">Shop</a>
              <a href="/policies/privacy">Privacy Policy</a>
              <a href="/policies/refund">Refund Policy</a>
              <a href="/policies/shipping">Shipping & Delivery</a>
              <a href="/policies/terms">Terms & Conditions</a>
            </div>
          </div>
          <p className={styles.copyright}>© 2026 UVEE. All rights reserved. A Complete Healthy Diet Store.</p>
        </div>
      </footer>
    </main>
  );
}
