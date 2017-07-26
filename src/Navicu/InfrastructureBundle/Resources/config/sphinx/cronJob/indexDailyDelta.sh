# Este comando de motor de busqueda se ejecutara cada 10 min.

indexer --rotate dailyPack_delta dailyRoom_delta
indexer --merge dailyPack dailyPack_delta --merge-dst-range deleted 0 0 --rotate
indexer --merge dailyRoom dailyRoom_delta --merge-dst-range deleted 0 0 --rotate