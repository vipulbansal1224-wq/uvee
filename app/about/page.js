import Navbar from '../../components/Navbar';
import ImageSlider from '../../components/ImageSlider';
import styles from '../page.module.css';

export default function About() {
  const images = [
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.11-PM-1.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.12-PM-1.jpeg',
    '/wp-content/uploads/2022/07/WhatsApp-Image-2022-08-20-at-7.49.13-PM-1.jpeg'
  ];

  return (
    <main className={styles.main}>
      <Navbar />
      <div style={{ height: '300px', overflow: 'hidden' }}>
        <ImageSlider images={images} />
      </div>
      <div style={{ padding: '60px 24px 80px', maxWidth: '800px', margin: '0 auto' }}>
        <h1 style={{ fontSize: '48px', fontWeight: '800', marginBottom: '24px', color: 'var(--primary)' }}>About Us</h1>
        
        <div style={{ fontSize: '18px', color: '#4a5568', lineHeight: '1.8' }}>
          <p style={{ marginBottom: '20px' }}>
            We specialize in premium quality Cashews, Pistachios and Almonds. Our product line includes different quality grades of plain whole cashew nuts, traditional drum roasted cashew nuts and a variety of flavored cashew nuts.
          </p>
          <p style={{ marginBottom: '20px' }}>
            Our products do not follow the industrial pattern of preservation and adulteration for a minimum nutrition loss of the groceries, as we aim to focus on a longer and healthier life for people and not shelf life of products.
          </p>
          <p>
            Right from flower to fruit to the processing and packaging, we employ fair trade practices and high quality standards to ensure that our customers enjoy only the best tasting cashews, while still being able to offer the best wholesale price for our suppliers.
          </p>
        </div>
      </div>
    </main>
  );
}
