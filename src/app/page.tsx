import Image from 'next/image';
import styles from './page.module.css';

const products = [
  { id: 1, name: "Premium Salted Almonds", price: "₹450", image: "/almonds.png" },
  { id: 2, name: "Classic Roasted Makhana", price: "₹250", image: "/makhana.png" },
  { id: 3, name: "Lemon Chilli Spiced Nuts", price: "₹300", image: "/lemon_chilli.png" },
  { id: 4, name: "Golden Roasted Cashews", price: "₹650", image: "/cashews.png" },
];

export default function Home() {
  return (
    <>
      <nav className={`${styles.navbar} ${styles['glass-panel']}`}>
        <div className={styles.navContainer}>
          <div className={styles.logo}>UVEE</div>
          <div className={styles.navLinks}>
            <a href="#" className={styles.navLink}>Home</a>
            <a href="#shop" className={styles.navLink}>Shop</a>
            <a href="#" className={styles.navLink}>About</a>
            <a href="#" className={styles.navLink}>Contact</a>
          </div>
        </div>
      </nav>

      <main className={styles.main}>
        <section className={styles.hero}>
          <div className={`${styles['glass-panel']} ${styles['animate-fade-in']}`} style={{ padding: '60px 40px', maxWidth: '800px', margin: '0 auto' }}>
            <h1 className={`${styles.heroTitle} ${styles['text-shadow']}`}>Finest Roasted Nuts & Spices</h1>
            <p className={styles.heroSubtitle}>
              Experience the perfect crunch and authentic flavors with UVEE. 
              Premium quality ingredients carefully selected and roasted to perfection.
            </p>
            <button className={styles['btn-primary']}>Explore Collection</button>
          </div>
        </section>

        <section id="shop" className={styles.section}>
          <div className={styles.container}>
            <h2 className={`${styles.sectionTitle} ${styles['text-shadow']}`}>Our Premium Selection</h2>
            <div className={styles.grid}>
              {products.map((product) => (
                <div key={product.id} className={`${styles.productCard} ${styles['glass-panel']}`}>
                  <div className={styles.imageWrapper}>
                    <Image 
                      src={product.image} 
                      alt={product.name} 
                      width={300} 
                      height={300} 
                      priority
                    />
                  </div>
                  <div className={styles.productInfo}>
                    <h3 className={styles.productName}>{product.name}</h3>
                    <p className={styles.productPrice}>{product.price}</p>
                    <button className={styles.addToCartBtn}>Add to Cart</button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>
    </>
  );
}
