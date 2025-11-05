<section class="history-header">
  <div class="icon-title">
    <span class="icon">🧾</span>
    <h1>ประวัติการซื้อ</h1>
  </div>
  <p class="subtitle">ตรวจสอบและจัดการสินค้าที่คุณเคยสั่งซื้อจาก <span>XY STORE</span></p>

  <!-- 🔹 ปุ่มย้อนกลับ -->
  <a href="shop.php" class="back-btn">⬅ กลับหน้าหลัก</a>
</section>

<style>
body {
  background: #0e0e0e; /* เข้ากับธีมหลัก */
  color: #fff;
  font-family: 'Prompt', sans-serif;
}

.history-header {
  text-align: center;
  margin: 100px auto 60px auto;
  color: #fff;
  animation: fadeIn 0.8s ease;
}

/* ===== หัวข้อ ===== */
.icon-title {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.icon {
  font-size: 2rem;
  background: linear-gradient(90deg, #b37bff, #7a3fff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.history-header h1 {
  font-size: 2.5rem;
  font-weight: 900;
  background: linear-gradient(90deg, #b37bff, #7a3fff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin: 0;
  letter-spacing: 1px;
}

.history-header .subtitle {
  color: #bbb;
  font-size: 1rem;
  margin-top: 8px;
}

.history-header .subtitle span {
  color: #b37bff;
  font-weight: 600;
}

/* 🔹 เส้นคั่น Gradient */
.history-header::after {
  content: "";
  display: block;
  width: 180px;
  height: 3px;
  margin: 20px auto 0;
  border-radius: 3px;
  background: linear-gradient(90deg, #b37bff, #7a3fff);
  box-shadow: 0 0 15px rgba(179, 123, 255, 0.6);
}

/* ===== ปุ่มย้อนกลับ ===== */
.back-btn {
  display: inline-block;
  margin-top: 30px;
  background: linear-gradient(90deg, #8b5cf6, #7c3aed);
  color: #fff;
  padding: 10px 24px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.25s;
  box-shadow: 0 0 15px rgba(139, 92, 246, 0.4);
}

.back-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 25px rgba(179, 123, 255, 0.7);
  background: linear-gradient(90deg, #9c66ff, #844cff);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
