const puppeteer = require('puppeteer');

(async () => {
  const payload = process.env.PAYLOAD;

  const browser = await puppeteer.launch({
    headless: 'new',
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox'
    ]
  });

  const page = await browser.newPage();

  // ปลุกเว็บ + รับ cookie
  await page.goto(
    'https://dkt.gt.tc/',
    {
      waitUntil: 'domcontentloaded',
      timeout: 60000
    }
  );

  // ส่ง webhook ต่อ
  const result = await page.evaluate(async (payload) => {

    const response = await fetch(
      'https://dkt.gt.tc/admin/webhook',
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: payload
      }
    );

    return await response.text();

  }, payload);

  console.log(result);

  await browser.close();
})();
