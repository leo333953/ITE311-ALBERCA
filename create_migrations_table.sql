USE lms_alberca;

CREATE TABLE IF NOT EXISTS migrations (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  version varchar(255) NOT NULL,
  class varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  namespace varchar(255) NOT NULL,
  time int(11) NOT NULL,
  batch int(11) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;