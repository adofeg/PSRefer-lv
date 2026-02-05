import { test, expect } from '@playwright/test';
import RegisterPage from '../../pages/RegisterPage.js';
import { loadTestData, SUPPORTED_LANGUAGES } from '../../helpers/dataLoader.js';

const {
  data: registerData,
  loadedLanguages: availableLanguages
} = loadTestData('register', SUPPORTED_LANGUAGES);

test.describe('Register Page - Multi-Language', () => {
  availableLanguages.forEach(lang => {
    test.describe(`Register in ${lang.toUpperCase()}`, () => {
      let registerPage;

      test.beforeEach(async ({ page }) => {
        // Mock Register Page (GET)
        await page.route('**/register', async route => {
            if (route.request().method() === 'GET') {
                await route.fulfill({
                    status: 200,
                    contentType: 'text/html',
                    body: `<html><body>
                        <form action="/register" method="POST">
                             <input id="name" name="name" />
                             <input type="email" name="email" />
                             <input type="password" name="password" />
                             <button type="submit">Register</button>
                        </form>
                    </body></html>`
                });
            } else {
                await route.fallback();
            }
        });

        registerPage = new RegisterPage(page, lang);
        await registerPage.navigate();
      });

      test('should register a new user (mocked)', async ({ page }) => {
        // Mock Register Endpoint
        await page.route('**/register', async route => {
          if (route.request().method() === 'POST') {
             await route.fulfill({
                status: 302,
                headers: { 'Location': 'http://localhost:8002/dashboard' }
             });
          } else {
             await route.continue();
          }
        });

        // Mock Dashboard (if needed, though login spec covers it, register might redirect there)
         await page.route('**/dashboard', async route => {
             await route.fulfill({
                status: 200,
                 headers: { 'Content-Type': 'application/json', 'X-Inertia': 'true' },
                body: JSON.stringify({
                    component: 'Dashboard',
                    props: { auth: { user: { name: 'New User' } }, errors: {} },
                    url: '/dashboard',
                    version: 'mock-version'
                })
             });
        });

         await registerPage.register();

         // Verify redirection
         await expect(page).toHaveURL(/\/dashboard/);
      });

      test('should show validation errors (mocked)', async ({ page }) => {
          await page.route('**/register', async route => {
              if (route.request().method() === 'POST') {
                   // Mock 422 behavior via Inertia props
                   await route.fulfill({
                      status: 200,
                      headers: { 'Content-Type': 'application/json', 'X-Inertia': 'true' },
                      body: JSON.stringify({
                          component: 'Auth/Register',
                          props: { errors: { email: 'The email has already been taken.' } },
                          url: '/register',
                          version: 'mock-version'
                      })
                   });
              } else {
                  await route.continue();
              }
          });

          await registerPage.register();
          // Assuming we have a way to check generic error or specific field error
          // For now, we'll check if we are still on register page or see some error
          // The Page Object might not have error selectors yet, so we use generic text search if unsure
           await expect(page.getByText('The email has already been taken.')).toBeVisible();
      });
    });
  });
});
