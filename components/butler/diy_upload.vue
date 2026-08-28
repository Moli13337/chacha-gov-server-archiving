<template>
	<div>
		<el-upload
			action=""
			:multiple="multiple"
			:accept="accept"
			:list-type="listType"
			:limit="limitFileNumber"
			:file-list="innerFileList"
			:before-upload="beforeUpload"
			:http-request="customUpload"
			:on-success="handleSuccess"
			:on-error="handleError"
			:on-exceed="handleExceed"
			:on-remove="handleRemove"
			:on-preview="handlePreview"
		>
			<el-button
				v-if="listType !== 'picture-card'"
				size="small"
				type="primary"
			>{{buttonText || '点击上传'}}</el-button>
			<i
				v-else
				class="el-icon-plus"
			></i>
			<div
				slot="tip"
				class="el-upload__tip"
				v-if="tip"
			>{{tip}}</div>
		</el-upload>
	</div>
</template>

<script>

import {
	UPLOAD_FILE
} from '@/utils/urls.js';

export default {
	model: {
		prop: 'fileList',
		event: 'change'
	},
	props: {
		tip: {
			type: String,
			default: '可上传多个文件，单个文件大小不超过100M'
		},
		buttonText: String,
		accept: {
			type: String,
			default: '*'
		},
		multiple: Boolean,
		listType: String,
		limitFileNumber: Number,
		limitSizePerFile: {
			type: Number,
			default: 100
		},
		fileList: Array
	},
	data() {
		return {
			innerFileList: this.fileList ? this.fileList : []
		};
	},
	watch: {
		fileList(val) {
			this.innerFileList = val ? val : [];
		}
	},
	methods: {
		beforeUpload(file) {
			const isLtLimitSize = file.size / 1024 / 1024 > this.limitSizePerFile;

			if (isLtLimitSize) {
				this.$message.error(`单个文件大小不能超过${this.limitSizePerFile}MB!`);
				return false;
			}

			return true;
		},
		customUpload({file, onProgress}) {
			let date = +new Date();

			let business_id = `wenjiangproject-${date}-${date.toString().substr(-5)}`;
			let formData = new FormData();

			formData.append('business_id', business_id);
			formData.append('file', file);

			let request = this.$axios.post(UPLOAD_FILE, formData, {
				onUploadProgress: ({lengthComputable, loaded, total}) => {
					if (lengthComputable) {
						onProgress({percent: Math.floor(loaded / total * 100) - 1});
					}
				},
				headers: {'Content-Type': 'multipart/form-data'}
			}).catch(error => {
				console.log(error);
			});

			request.abort = () => {
				console.log('request abort');
			};
			return request;
		},
		handleSuccess(response, file, fileList) {
			console.log('success', response, file);

			this.innerFileList = fileList;
			this.innerFileList.forEach(item => {
				if (item.uid === file.uid) {
					item.url = response.url;
				}
			});
			this.$emit('change', this.innerFileList);
		},
		handleError(response, file, fileList) {
			console.log('error', response, file);

			this.innerFileList = fileList;
			this.$emit('change', this.innerFileList);
			this.$message.error(response.error_data && response.error_data.message || '上传失败');
		},
		handleExceed() {
			this.$message.error(`最多只能上传${this.limitFileNumber}个文件`);
		},
		handleRemove(file, fileList) {
			this.innerFileList = fileList;
			this.$emit('change', this.innerFileList);
		},
		handlePreview(file) {
			let _url = '';

			if (file.response && file.response.data && file.response.data.url) {
				_url = file.response.data.url;
			} else if (file.url) {
				_url = file.url;
			}
			if (_url) {
				window.open(_url);
			}
		}
	}
};
</script>
