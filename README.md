# Investalo News Feed 📰

Ein hochperformanter News-Aggregator für WordPress, der spezialisiert ist auf die Anforderungen von Tradern und Finanzbegeisterten.

## ⚙️ Technische Features
- **Cron-Job Integration:** Das Plugin nutzt den WordPress Cron (`hourly`), um Feeds im Hintergrund zu aktualisieren. Keine Verzögerung beim Seitenaufruf.
- **Transients API:** Daten werden stündlich im Cache gespeichert, was API-Requests minimiert und die Performance maximiert.
- **Smart Sorting:** Kombiniert mehrere RSS-Quellen (z.B. Reuters, FAZ) und sortiert diese automatisch nach Aktualität.
- **Clean UI:** Minimalistisches Fintech-Design mit dezenten CSS-Gradients und voller Responsivität.

## 🛠 Installation
1. Repository klonen oder `.php` Datei herunterladen.
2. In den `/wp-content/plugins/` Ordner kopieren.
3. Plugin aktivieren und den Shortcode `[investalo_news]` einbinden.

---
*Entwickelt als Teil der Investalo Akademie Suite für professionelles Trading-Equipment.*
