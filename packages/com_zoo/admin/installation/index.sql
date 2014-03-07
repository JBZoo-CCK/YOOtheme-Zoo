-- --------------------------------------------------------

ALTER TABLE `#__zoo_category`
	ADD UNIQUE `ALIAS_INDEX` (`alias`),
	ADD INDEX `PUBLISHED_INDEX` (`published`),
	ADD INDEX `APPLICATIONID_ID_INDEX` (`application_id`, `published`, `id`),
    ADD INDEX `APPLICATIONID_ID_INDEX2` (`application_id`, `id`);

-- --------------------------------------------------------

ALTER TABLE `#__zoo_category_item`
	ADD INDEX `ITEMID_INDEX` (`item_id`),
	ADD INDEX `CATEGORYID_INDEX` (`category_id`);

-- --------------------------------------------------------

ALTER TABLE `#__zoo_comment`
	ADD INDEX `STATE_INDEX` (`state`),
	ADD INDEX `CREATED_INDEX` (`created`),
	ADD INDEX `ITEMID_INDEX` (`item_id`),
	ADD INDEX `AUTHOR_INDEX` (`author`),
	ADD INDEX `ITEMID_STATE_INDEX` (`item_id`, `state`);

-- --------------------------------------------------------

ALTER TABLE `#__zoo_item`
	ADD UNIQUE `ALIAS_INDEX` (`alias`),
	ADD INDEX `PUBLISH_INDEX` (`publish_up`, `publish_down`),
	ADD INDEX `STATE_INDEX` (`state`),
	ADD INDEX `ACCESS_INDEX` (`access`),
	ADD INDEX `CREATED_BY_INDEX` (`created_by`),
	ADD INDEX `NAME_INDEX` (`name`),
	ADD INDEX `APPLICATIONID_INDEX` (`application_id`),
	ADD INDEX `TYPE_INDEX` (`type`),
	ADD INDEX `MULTI_INDEX` (`application_id`, `access`, `state`, `publish_up`, `publish_down`),
	ADD INDEX `MULTI_INDEX2` (`id`, `access`, `state`, `publish_up`, `publish_down`),
	ADD INDEX `ID_APPLICATION_INDEX` (`id`, `application_id`),
	ADD FULLTEXT `SEARCH_FULLTEXT` (`name`);

-- --------------------------------------------------------

ALTER TABLE `#__zoo_search_index`
	ADD FULLTEXT `SEARCH_FULLTEXT` (`value`);

-- --------------------------------------------------------

ALTER TABLE `#__zoo_submission`
	ADD UNIQUE `ALIAS_INDEX` (`alias`);

-- --------------------------------------------------------

ALTER TABLE `#__zoo_tag`
	ADD UNIQUE `NAME_ITEMID_INDEX` (`name`, `item_id`);