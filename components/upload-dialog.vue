<template>
	<el-dialog
		title="上传附件"
		:visible.sync="uploadDialogVisible"
		width="500px"
		:close-on-click-modal=false
		:before-close="uploadModelClose"
	>
		<el-upload
			v-if="!currentMaterial || !uploadedMaterialMap[currentMaterial.id] || !uploadedMaterialMap[currentMaterial.id].length"
			class="upload-container"
			drag
			multiple
			action=""
			name="file"
			:file-list="currentMaterial && uploadedMaterialMap[currentMaterial.id] || []"
			:data="{ business_id: businessId }"
			:http-request="customUpload"
			:beforeUpload="beforeUpload"
			:on-progress="handleUploadProgress"
			:on-change="handleUploadChange"
			:on-error="handleUploadError"
			:on-remove="handleUploadRemove"
		>
			<img
				src="~assets/images/upload-icon-tip.jpg"
				class="upload-icon"
			>
			<div class="el-upload__btn">
				<el-button
					type="primary"
					@click="handleSelect"
				>点击选择</el-button>
			</div>
			<p
				class="el-upload__tip"
				slot="tip"
			>或将附件拖到这里，单次最多可选附件数量为300份</p>
		</el-upload>
		<div
			v-else
			class="upload-file-box"
		>
			<p class="box-title">已选择{{uploadedMaterialMap[currentMaterial.id].length}}份附件，最多可选择300份附件</p>
			<ul class="upload-file-list">
				<li
					class="upload-file-item"
					v-for="(item, index) in uploadedMaterialMap[currentMaterial.id]"
					:key="index"
				>
					<img
						class="file-thumbnail"
						src="~/assets/images/icon-image.jpg"
					/>
					<div class="file-content-wrap">
						<p class="file-name">{{item.file_name}}</p>
						<span v-if="item.status === 'success'">上传完成</span>
						<span
							class="upload-fail-tip"
							v-else-if="item.status === 'fail'"
						>上传失败:{{item.errorMsg}}</span>
						<el-progress
							class="file-upload-progress"
							:percentage="item.percent"
							v-else
						/>
					</div>
					<img
						class="file-remove-btn"
						src="~assets/images/ic_close@2x.png"
						@click="handleUploadRemove(item)"
					/>
				</li>
			</ul>
			<div class="box-action-buttons">
				<el-button
					class="action-button"
					type="primary"
					@click="handleUploadClose"
					v-if="isUpload"
				>确定</el-button>
				<el-button
					class="action-button"
					type="default"
					@click="handleReuploadClick(currentMaterial)"
				>重新选择</el-button>
			</div>
		</div>
	</el-dialog>
</template>

<script>
export default {
	methods: {
		// 文件上传限制
		beforeUpload(file) {
			let filename = file.name || '';
			let temp = filename.substring(filename.lastIndexOf('.') + 1) || '';
			let ext = temp.toLocaleLowerCase();
			let extArr = ['jpg', 'doc', 'png', 'bmp', 'jpeg', 'wps', 'docx', 'pdf', 'xls', 'xlsx'];

			if (extArr.indexOf(ext) < 0) {
				if (this.errorPrompt) {
					this.$message({
						message: '上传文件只能是JPG，JPEG, PNG, BMP, DOC，WPS，DOCX，PDF，XLS，XLSX格式的附件',
						type: 'warning'
					});
					this.errorPrompt = false;
				}
				return false;
			}

			const isLt2M = file.size / 1024 / 1024 < 20;

			if (!isLt2M) {
				if (this.Oversize) {
					this.$message({
						message: '上传文件大小不能超过 20MB!',
						type: 'warning'
					});
					this.Oversize = false;
				}
				return false;
			}

			return true;
		},
		// 自定义上传请求
		customUpload({file, data, filename, onProgress}) {
			let params = new FormData();

			params.append(filename, file);
			if (data) {
				for (let key in data) {
					params.append(key, data[key]);
				}
			}
			return this.$axios.post(UPLOAD_FILE, params, {
				onUploadProgress: ({lengthComputable, loaded, total}) => {
					if (lengthComputable) {
						onProgress({percent: Math.floor(loaded / total * 100)});
					}
				},
				headers: {'Content-Type': 'multipart/form-data'}
			});
		},
		// 处理文件上传中
		handleUploadChange(file) {
			let theFile = {
				uid: file.uid,
				status: file.status,
				file_name: file.name,
				file_type: this.currentMaterial.type,
				project_materials_id: this.currentMaterial.id,
			};

			if (file.response) {
				theFile.file_url = file.response.url;
				theFile.created_at = file.response.created_at;
			}

			let files = this.uploadedMaterialMap[this.currentMaterial.id] || [];
			let index = files.findIndex(item => item.uid === file.uid);

			if (index >= 0) {
				files[index] = {
					...files[index],
					...theFile,
				};
			} else {
				files.push(theFile);
			}

			// 保存为新对象以触发UI刷新
			this.uploadedMaterialMap = {
				...this.uploadedMaterialMap,
				[this.currentMaterial.id]: files
			};
		},
		// 上传失败的回调
		handleUploadError(err, file) {
			let errorMsg = err && err.message || '未知原因';

			let files = this.uploadedMaterialMap[this.currentMaterial.id] || [];
			let index = files.findIndex(item => item.uid === file.uid);

			if (index >= 0) {
				files[index] = {
					...files[index],
					errorMsg
				};

				// 保存为新对象以触发UI刷新
				this.uploadedMaterialMap = {
					...this.uploadedMaterialMap,
					[this.currentMaterial.id]: files
				};
			}
		},
		// 上传中的回调
		handleUploadProgress(event, file) {
			// 进度减一，为了避免上传进度为100%卡住的问题
			let percent = event.percent > 1 ? event.percent - 1 : event.percent;

			let files = this.uploadedMaterialMap[this.currentMaterial.id] || [];
			let index = files.findIndex(item => item.uid === file.uid);

			if (index >= 0) {
				files[index] = {
					...files[index],
					percent: percent
				};

				// 保存为新对象以触发UI刷新
				this.uploadedMaterialMap = {
					...this.uploadedMaterialMap,
					[this.currentMaterial.id]: files
				};
			}
		},
		// 删除某一个上传文件
		handleUploadRemove(file) {
			let files = this.uploadedMaterialMap[this.currentMaterial.id] || [];
			let removedFiles = files.filter(item => item.uid !== file.uid);

			// 保存为新对象以触发UI刷新
			this.uploadedMaterialMap = {
				...this.uploadedMaterialMap,
				[this.currentMaterial.id]: removedFiles
			};
		},
	}
};
</script>
