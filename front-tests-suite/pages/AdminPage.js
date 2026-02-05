import Base from './core/Base.js';
import { expect } from '@playwright/test';

class AdminPage extends Base {
  constructor(page, language = 'es') {
    super(page);
    this.language = language;
    this.pageData = this.loadPageData('admin', language);
  }

  async navigate() {
    await this.page.goto(this.pageData.url);
    await this.page.waitForLoadState('domcontentloaded');
  }

  async verifyDashboard() {
    const heading = this.page.locator(this.pageData.dashboard.headingSelector);
    await expect(heading).toBeVisible();
    await expect(heading).toHaveText(this.pageData.dashboard.headingText);
  }

  async verifyAnalyticsWidgets() {
    const widgets = this.page.locator(this.pageData.analytics.widgetSelector);
    await expect(widgets).toHaveCount(this.pageData.analytics.expectedCount);
  }
}

export default AdminPage;
