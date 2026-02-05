import { expect } from '@playwright/test';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

class Base {
  constructor(page) {
    this.page = page;
  }

  /**
   * Loads the JSON data for the specific page and language
   * @param {string} pageName - The name of the folder in data/
   * @param {string} language - The language code (es, en, etc)
   */
  loadPageData(pageName, language) {
    const dataPath = path.join(__dirname, '..', '..', 'data', pageName, `${language}.json`);
    if (!fs.existsSync(dataPath)) {
        throw new Error(`Data file not found: ${dataPath}`);
    }
    const rawData = fs.readFileSync(dataPath, 'utf-8');
    return JSON.parse(rawData);
  }

  async navigate(url) {
    await this.page.goto(url);
    await this.page.waitForLoadState('domcontentloaded');
  }
}

export default Base;
