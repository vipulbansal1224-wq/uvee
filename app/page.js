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
            <div style={{ flex: 1, marginLeft: '60px' }}>
              <h4 style={{ fontSize: '20px', color: 'white', marginBottom: '16px' }}>Send Enquiry</h4>
              <form style={{ display: 'flex', gap: '8px', marginBottom: '24px' }}>
                <input type="email" placeholder="Your Email Address" style={{ padding: '12px', borderRadius: '4px', border: 'none', flex: 1, outline: 'none' }} />
                <button type="button" style={{ padding: '12px 24px', background: 'var(--primary)', color: 'white', border: 'none', borderRadius: '4px', fontWeight: 'bold', cursor: 'pointer' }}>Send</button>
              </form>
              
              <h4 style={{ fontSize: '16px', color: 'white', marginBottom: '16px' }}>Follow Us</h4>
              <div style={{ display: 'flex', gap: '16px' }}>
                <a href="#" style={{ color: 'white', background: '#333', width: '40px', height: '40px', display: 'flex', alignItems: 'center', justifyContent: 'center', borderRadius: '50%' }}>
                  <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.597 0 0 .597 0 1.325v21.351C0 23.403.597 24 1.325 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116c.73 0 1.323-.597 1.323-1.324V1.325C24 .597 23.403 0 22.675 0z"/></svg>
                </a>
                <a href="#" style={{ color: 'white', background: '#333', width: '40px', height: '40px', display: 'flex', alignItems: 'center', justifyContent: 'center', borderRadius: '50%' }}>
                  <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="#" style={{ color: 'white', background: '#333', width: '40px', height: '40px', display: 'flex', alignItems: 'center', justifyContent: 'center', borderRadius: '50%' }}>
                  <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723 10.054 10.054 0 01-3.127 1.184 4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
              </div>
            </div>
          </div>
          <p className={styles.copyright}>© 2026 UVEE. All rights reserved. A Complete Healthy Diet Store.</p>
        </div>
      </footer>
    </main>
  );
}
