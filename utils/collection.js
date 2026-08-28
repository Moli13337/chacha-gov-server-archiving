import {
	COLLECTION_SAVE,
	COLLECTION_CANCEL
} from './urls.js';
export default {
	data() {
		return {
			isCollection: 0
		};
	},
	methods: {

		// 收藏1-宏观政策 4-申报公示公告 7-活动公示公告 8-项目 10-拨款公示公告 16-中介机构
		handleCollection() {
			let params = {
				obj_enc_id: this.collection_enc_id,
				obj_type: this.collection_obj_type,
			};


			this.$axios.post(COLLECTION_SAVE, params).then(() => {
				this.$message.success('收藏成功');
				this.isCollection++;
				console.log('isCollection', this.isCollection);
			}).catch(error => {
				this.$message.error(error.message);
			});
		},

		// 取消收藏
		handleCancelCollection() {
			let params = {
				obj_enc_id: this.collection_enc_id,
				obj_type: this.collection_obj_type,
			};

			this.$axios.post(COLLECTION_CANCEL, params).then(() => {
				this.$message.success('取消收藏成功');
				this.isCollection = 0;
				console.log('isCollection', this.isCollection);
			}).catch(error => {
				this.$message.error(error.message);
			});
		},
	}
};

