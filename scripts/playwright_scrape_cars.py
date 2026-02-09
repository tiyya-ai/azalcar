import asyncio
from pathlib import Path

from playwright.async_api import async_playwright

URL = "https://www.azalcars.com/"
OUTPUT = Path("cars_homepage_playwright.html")
USER_AGENT = (
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
    "AppleWebKit/537.36 (KHTML, like Gecko) "
    "Chrome/120.0.0.0 Safari/537.36"
)


async def scrape() -> None:
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page(user_agent=USER_AGENT)
        await page.goto(URL, wait_until="networkidle", timeout=60000)
        await page.wait_for_timeout(2000)
        html = await page.content()
        OUTPUT.write_text(html, encoding="utf-8")
        await browser.close()
        print(f"Saved HTML -> {OUTPUT.resolve()}")


def main() -> None:
    asyncio.run(scrape())


if __name__ == "__main__":
    main()
