import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse
from collections import deque
import re

def crawl_website(base_url):
    visited = set()  # Track visited URLs
    queue = deque([base_url])  # URLs to process
    discovered_urls = set()  # All discovered URLs

    base_domain = urlparse(base_url).netloc

    while queue:
        current_url = queue.popleft()
        if current_url in visited:
            continue  # Skip if already visited

        try:
            response = requests.get(current_url, timeout=10)
            response.raise_for_status()  # Raise exception for HTTP errors
            visited.add(current_url)
            discovered_urls.add(current_url)

            soup = BeautifulSoup(response.text, 'html.parser')
            for link in soup.find_all('a', href=True):
                full_url = urljoin(current_url, link['href'])  # Resolve relative URLs
                parsed_url = urlparse(full_url)

                # Include only subdomains and paths related to the base domain
                if base_domain in parsed_url.netloc and full_url not in visited:
                    queue.append(full_url)
        except Exception as e:
            print(f"Error accessing {current_url}: {e}")

    return sorted(discovered_urls)

# Beautify output for display
def beautify_output(urls):
    print("\n--- Discovered URLs ---\n")
    for url in urls:
        print(url)
    print(f"\nTotal URLs Found: {len(urls)}")

# Example usage
if __name__ == "__main__":
    base_url = "https://toshkentinvest.uz/"  # Change this to your target URL
    all_urls = crawl_website(base_url)
    beautify_output(all_urls)
