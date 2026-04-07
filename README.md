# BetterElo

A PocketMine-MP 5.x plugin that adds an Elo ranking system to your server.

## Features

- **Elo System**: Players gain +10 Elo on kill, lose -10 Elo on death
- **Customizable**: Easily configurable through config.yml
- **Commands**:
  - `/myelo` - View your current Elo
  - `/topelo` - View the top 10 players leaderboard (UI form)
- **Database**: Uses SQLite via Himari library for async database operations

## Requirements

- PocketMine-MP 5.x
- Himari library (included)
- FormX library (included)

## Installation

1. Download the plugin
2. Place it in your `plugins/` folder
3. Restart the server

## Configuration

Edit `config.yml`:

```yaml
database:
  driver: sqlite
  parameters:
    filename: "{@DATAFOLDER}elo.db"

settings:
  elo_per_kill: 10
  elo_per_death: 10

messages:
  myelo: "§7Your Elo: §b{ELO}"
  no_elo: "§cYou don't have any elo yet!"
  elo_gained: "§b+{AMOUNT} §7Elo"
  elo_lost: "§c-{AMOUNT} §7Elo"

  leaderboard_title: "§l§8Top §b10 §8Elo"
  leaderboard_format: "§b#{POSITION} §7{PLAYER}: §b{ELO}"
  leaderboard_empty: "§cNo players in the leaderboard!"
  leaderboard_footer: "§7Use §b/myelo §7to view your elo"
```

## License

MIT License