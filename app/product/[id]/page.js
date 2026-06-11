import { notFound } from 'next/navigation';
import { products } from '../../../data/products';
import Navbar from '../../../components/Navbar';
import styles from './ProductPage.module.css';

export function generateStaticParams() {
  return products.map((p) => ({
    id: p.id,
  }));
}

export default function ProductPage({ params }) {
  const product = products.find(p => p.id === params.id);
  
  if (!product) {
    notFound();
  }

  return (
    <main className={styles.main}>
      <Navbar />
      <div className={styles.container}>
        <div className={styles.productGrid}>
          <div className={styles.imageSection}>
            <div className={styles.imageWrapper}>
              <img src={product.image} alt={product.name} className={styles.mainImage} />
            </div>
          </div>
          
          <div className={styles.infoSection}>
            <span className={styles.category}>{product.category}</span>
            <h1 className={styles.title}>{product.name}</h1>
            <div className={styles.priceContainer}>
              <span className={styles.price}>₹{product.price.toFixed(2)}</span>
            </div>
            
            <p className={styles.description}>
              {product.description || "Premium quality dried fruits and snacks, perfectly roasted and seasoned for the best taste."}
            </p>
            
            <div className={styles.actions}>
              <div className={styles.quantity}>
                <button>-</button>
                <span>1</span>
                <button>+</button>
              </div>
              <button className={styles.addToCartBtn}>Add to Cart</button>
            </div>
            
            <div className={styles.features}>
              <div className={styles.feature}>✅ Premium Quality</div>
              <div className={styles.feature}>✅ 100% Natural</div>
              <div className={styles.feature}>✅ Fast Delivery</div>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
