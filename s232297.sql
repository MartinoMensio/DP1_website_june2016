DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL, -- SHA1 is 160bit -> 20bytes -> 40 hex char
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_time` int(11) NOT NULL, -- seconds after 00:00:00
  `starting_hour` int(11) NOT NULL,
  `starting_minute` int(11) NOT NULL,
  `ending_hour` int(11) NOT NULL,
  `ending_minute` int(11) NOT NULL,
  `machine` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);

-- values

INSERT INTO `users` (`id`, `name`, `surname`, `email`, `password`) VALUES
(1, 'u1', 'test', 'u1@p.it', SHA1('p')),
(2, 'u2', 'test', 'u2@p.it', SHA1('p2')),
(3, 'u3', 'test', 'u3@p.it', SHA1('p3'));

INSERT INTO `reservations` (`starting_hour`, `starting_minute`, `ending_hour`, `ending_minute`, `machine`, `user_id`) VALUES
-- u1
(12, 00, 13, 00, 0, 1),
(13, 30, 13, 45, 1, 1),
-- u2
(13, 50, 17, 00, 0, 2),
(11, 30, 11, 45, 1, 2),
-- u3
(08, 00, 8, 30, 0, 3),
(17, 00, 18, 00, 1, 3),
-- overlapping
(18, 00, 19, 30, 0, 1),
(19, 00, 20, 00, 1, 2);