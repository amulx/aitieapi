CREATE TRIGGER topic_delete AFTER DELETE ON tbl_topic FOR EACH ROW DELETE
FROM
	tbl_collect
WHERE
	topicid = OLD.topicid;
  
# 当贴子删除时，自动删除该贴在收藏表的的所有记录
