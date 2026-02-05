import { test, expect } from '@playwright/test';
import AdminPage from '../../pages/AdminPage.js';
import LoginPage from '../../pages/LoginPage.js';
import { loadTestData, SUPPORTED_LANGUAGES } from '../../helpers/dataLoader.js';

const {
  data: adminData,
  loadedLanguages: availableLanguages
} = loadTestData('admin', SUPPORTED_LANGUAGES);

const {
  data: loginData
} = loadTestData('login', SUPPORTED_LANGUAGES);

test.describe('Admin Module', () => {
  availableLanguages.forEach(lang => {
    test.describe(`Admin in ${lang.toUpperCase()}`, () => {
      let adminPage;
      let loginPage;

      test.beforeEach(async ({ page }) => {
        // Authenticate as Admin
        loginPage = new LoginPage(page, lang);

        // Mock Login Page (GET)
        await page.route('**/login', async route => {
            if (route.request().method() === 'GET') {
                await route.fulfill({
                    status: 200,
                    contentType: 'text/html',
                    body: `<html><body>
                        <form action="/login" method="POST">
                            <input type="email" name="email" />
                            <input type="password" name="password" />
                            <button type="submit">Log in</button>
                        </form>
                    </body></html>`
                });
            } else {
                await route.continue();
            }
        });

        await loginPage.navigate();

        // Mock Login
        await page.route('**/login', async route => {
            if (route.request().method() === 'POST') {
                 await route.fulfill({ status: 302, headers: { 'Location': 'http://localhost:8002/admin' } });
            } else { await route.continue(); }
        });

        // Mock Admin Dashboard Load
        await page.route('**/admin', async route => {
             const isJson = route.request().headerValue('X-Inertia');
             if (!isJson && route.request().method() === 'GET') {
                 await route.fulfill({
                     status: 200,
                     contentType: 'text/html',
                     body: `<html><body>
                        <h1 class="text-2xl">Panel de Administración</h1>
                        <div class="analytics-widget">Widget 1</div>
                        <div class="analytics-widget">Widget 2</div>
                        <div class="analytics-widget">Widget 3</div>
                        <div class="analytics-widget">Widget 4</div>
                     </body></html>`
                 });
             } else {
                 await route.fulfill({
                    status: 200,
                    headers: { 'Content-Type': 'application/json', 'X-Inertia': 'true' },
                    body: JSON.stringify({
                        component: 'Admin/Dashboard',
                        props: { auth: { user: { name: 'Admin User', role: 'admin' } }, stats: {} },
                        url: '/admin'
                    })
                 });
             }
        });

        await loginPage.login('admin@example.com', 'password');

        adminPage = new AdminPage(page, lang);
      });

      test('should display admin dashboard and widgets (mocked)', async ({ page }) => {
        await adminPage.navigate();
        await adminPage.verifyDashboard();
        await adminPage.verifyAnalyticsWidgets();
      });
    });
  });
});
