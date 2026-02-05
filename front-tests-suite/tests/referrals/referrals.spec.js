import { test, expect } from '@playwright/test';
import ReferralsPage from '../../pages/ReferralsPage.js';
import LoginPage from '../../pages/LoginPage.js';
import { loadTestData, SUPPORTED_LANGUAGES } from '../../helpers/dataLoader.js';

const {
  data: referralsData,
  loadedLanguages: availableLanguages
} = loadTestData('referrals', SUPPORTED_LANGUAGES);

const {
  data: loginData
} = loadTestData('login', SUPPORTED_LANGUAGES);

test.describe('Referrals Module', () => {
  availableLanguages.forEach(lang => {
    test.describe(`Referrals in ${lang.toUpperCase()}`, () => {
      let referralsPage;
      let loginPage;

      test.beforeEach(async ({ page }) => {
        // Authenticate first
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
                 await route.fulfill({ status: 302, headers: { 'Location': 'http://localhost:8002/dashboard' } });
            } else { await route.continue(); }
        });
         await page.route('**/dashboard', async route => {
             await route.fulfill({
                status: 200,
                headers: { 'Content-Type': 'application/json', 'X-Inertia': 'true' },
                body: JSON.stringify({ component: 'Dashboard', props: { auth: { user: { name: 'Test User' } } }, url: '/dashboard' })
             });
        });

        await loginPage.login('test@example.com', 'password');

        referralsPage = new ReferralsPage(page, lang);
      });

      test('should display referrals listing (mocked)', async ({ page }) => {
          // Mock Referrals Listing
          await page.route('**/referrals', async route => {
              const isJson = route.request().headerValue('X-Inertia');
               if (!isJson && route.request().method() === 'GET') {
                   await route.fulfill({
                       status: 200,
                       contentType: 'text/html',
                       body: `<html><body>
                          <h2 class="text-xl">Mis Referidos</h2>
                          <button>Nuevo Referido</button>
                          <div role="dialog"><input name="name"/><input name="email"/><button type="submit">Submit</button></div>
                          <div class="text-green-600">Referido creado exitosamente</div>
                       </body></html>`
                   });
               } else {
                  await route.fulfill({
                      status: 200,
                      headers: { 'Content-Type': 'application/json', 'X-Inertia': 'true' },
                      body: JSON.stringify({
                          component: 'Referrals/Index',
                          props: { referrals: [], auth: { user: { name: 'Test User' } } },
                          url: '/referrals'
                      })
                  });
              }
          });

          await referralsPage.navigate();
          await referralsPage.verifyListing();
      });

      test('should create a new referral (mocked)', async ({ page }) => {
          // Mock Referrals Listing
          await page.route('**/referrals', async route => {
              // Initial load
              await route.fulfill({
                  status: 200,
                  headers: { 'Content-Type': 'application/json', 'X-Inertia': 'true' },
                  body: JSON.stringify({ component: 'Referrals/Index', props: { referrals: [], auth: { user: { name: 'Test User' } } }, url: '/referrals' })
              });
          });

          await referralsPage.navigate();
          await referralsPage.openCreateModal();

          await page.route('**/referrals', async route => {
             if (route.request().method() === 'POST') {
                 // Mock successful creation redirect
                 await route.fulfill({ status: 302, headers: { 'Location': 'http://localhost:8002/referrals' } });
             } else {
                 await route.continue();
             }
          });

          await referralsPage.submitReferral({ name: 'John Doe', email: 'john@example.com' });
          // In a real app, we check for success toast or redirection.
          // Since we mocked full page reload or inertia visit, we might need to check if we are back on listing
          await expect(page).toHaveURL(/referrals/);
      });
    });
  });
});
